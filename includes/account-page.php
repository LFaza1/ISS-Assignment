 <ul class="nav nav-pills">
    <li><a href="account.php?action=view">View Accounts</a></li>
    <li><a href="account.php?action=check_upload">Upload Checks</a></li>
    <li><a href="account.php?action=check_view">View Checks</a></li>
    <li><a href="account.php?action=logout">Logout</a></li>
</ul>

<?php

$action = 'view';
if (array_key_exists('action', $_GET)) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
} elseif (array_key_exists('action', $_POST)) {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
}

$action = htmlspecialchars($action);

// POST actions
if ($action === "add_account") {

    $query = "SELECT * FROM accounts WHERE userid=? AND accountname=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $_SESSION['userid'], $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "An account of that name already exists";
    } else {
        if (isset($_POST['name'])) {
            $name = htmlspecialchars($_POST['name']);
        
            $query = "INSERT INTO accounts (accountname, userid, balance) VALUES (?, ?, 0)";
            $stmt = $mysqli->prepare($query);
            if (!$stmt) {
                echo "Failed to prepare statement: (" . $mysqli->errno . ") " . $mysqli->error;
                exit();
            }
            
            $stmt->bind_param("ss", $name, $_SESSION['userid']);
            $executionResult = $stmt->execute();
            
            if (!$executionResult) {
                echo "Failed to add account: (" . $stmt->errno . ") " . $stmt->error;
                exit();
            }
        } else {
            echo "Error: The 'name' field is missing from the form data.";
        }
        
        echo "Account added successfully.";
        
    }
    
    $action = "view";

} else if ($action == "upload") {
if (isset($_POST["action"]) && $_POST["action"] == "upload") {
    $allowed_types = ["image/png", "image/jpeg"];
    $check_file = $_FILES["check_file"]["type"];
    $file_size = $_FILES["check_file"]["size"];

    if (!in_array($check_file, $allowed_types)) {
        echo "<div class=\"alert alert-danger\" role=\"alert\">Error: Only PNG and JPEG files are allowed!</div>";
    } elseif ($file_size > (3 * 1024 * 1024)) { // 3 MB limit
        echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File size should not exceed 1 MB!</div>";
    } else {
        $query = "SELECT * FROM checks WHERE userid=? AND name=?";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $_SESSION['userid'], $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 1) {
            echo "A check of that name already exists<br>";
        } else {
            if (count($_FILES) > 0 && array_key_exists('check_file', $_FILES)) {
                $upload_filename_items = explode(".", $_FILES['check_file']['name']);
                $ext = $upload_filename_items[count($upload_filename_items) - 1];

                $ext = preg_replace("/[^a-zA-Z0-9\.]/", "", $ext);

                $filename = $_SESSION['user'] . "." . $name . "." . $ext;
                $src = $_FILES['check_file']['tmp_name'];

                $full_path = dirname(__FILE__) . "/checks/" . $filename;
                $move_result = move_uploaded_file($src, $full_path);

                if ($move_result == TRUE) {
                    if (isset($_POST['Filename'])) {
                        $name = htmlspecialchars($_POST['Filename']);

                        $insert = "INSERT INTO checks (userid, name, filename) VALUES (?, ?, ?)";
                        $stmt = $mysqli->prepare($insert);
                        $stmt->bind_param("sss", $_SESSION['userid'], $name, $filename);
                        $stmt->execute();

                        if ($stmt) {
                            echo "<div>Check uploaded!</div>";
                        } else {
                            echo "<div>Check insert failed!</div>";
                        }
                    }
                } else {
                    echo "<div>Upload failed! (Perhaps the file was too big)</div>";
                }
            }
        }
    }
}


    
    $action = "check_view";
}



if ($action == "logout") {
    // Clear all session data
    $_SESSION = array();
    session_destroy();
    echo "You have been logged out";

} elseif ($action=="view") {

    $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE userid=?");
    $stmt->bind_param("i", $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
         echo "Failed to query: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    echo "<h3>Accounts</h3>\n<table class='table'>\n<tr><th>Account Name</th><th>Balance</th></tr>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            echo "<tr><td>" . $row['accountname'] . "</td><td>" . $row['balance'] . "</td></tr>";
        }
    } else {
        echo "<tr><td>You have no accounts</td><td>-</td></tr>";
    }
    echo "</table>";
    echo "<br><form method='post' action='account.php'><input type='text' name='name' /><input type='hidden' name='action' value='add_account'/><button  class='btn btn-default' type='submit'>Add Account</button></form>";
    

} elseif ($action=="check_upload") {

    echo "<div class=\"alert alert-warning\" role=\"alert\">Be sure your picture has your Manatee Bank card clearly visible!</div>";
    echo "<div class=\"alert alert-warning\" role=\"alert\">It may take awhile to process the check</div>";
    
    echo "<br><form method='post' action='account.php' enctype=\"multipart/form-data\"> ";
    echo "File Name: <input type='text' name='Filename' required />";
    echo "<br>";
    echo "File: <input type='file' name='check_file' />";
    echo "<br>";
    echo "<input type='hidden' name='action' value='upload'/>";
    
    echo "<button class='btn btn-default'  type='submit'>Upload Check</button></form>";
    
    
} elseif ($action=="check_view") {

    $query = "SELECT * FROM checks WHERE userid='" . $_SESSION['userid'] . "'";

    $result = $mysqli->query($query);

	if (!$result) {
		 echo "Failed to query: (" . $mysqli->errno . ") " . $mysqli->error;
	}

    echo "<h3>Checks</h3>\n<table class='table'>\n<tr><th>Check Name</th><th>View</th></tr>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            echo "<tr><td>" . $row['name'] . "</td><td><a href='/ISS-Assignment/includes/checks/" . $row['filename'] . "'>View</a></td></tr>";
        }
    } else {
        echo "<tr><td>You have no checks</td><td>-</td></tr>";
    }
    echo "</table>";
} else {
    echo "Invalid action $action";
}

?>
