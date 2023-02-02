<?php
include_once __DIR__ .'/libs/csrf/csrfprotector.php';

// Initialise CSRFProtector library
csrfProtector::init();
include 'config.php';
include "includes/database.php";

$ERROR = "";
$OKAY = FALSE;

if (array_key_exists('username', $_POST) && array_key_exists('password', $_POST) && array_key_exists('fullname', $_POST))  {

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
	$password = md5($_POST['password']);

    $stmt->execute();
    $result = $stmt->get_result();

	if (!$result) {
		$ERROR = "Failed to query: (" . $mysqli->errno . ") " . $mysqli->error;
	} else {
        if ($result->num_rows > 0) {
            $ERROR = "The username you selected is already being used, please try another.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO users (username, fullname, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $fullname, $password);
            $result = $stmt->execute();

            if ($result === TRUE) {
                $OKAY = TRUE;
            } else {
                $ERROR = "Insert failed";
            }
        }
    }

    if (!$mysqli->connect_errno) {
        $mysqli->close();
    }

}

?>
<?php include 'includes/header.php' ?>

<main class="container">

    <p>
    Signing up for an account is fast and easy!
    </p>


    <?php if ($ERROR != "") : ?>
    <div class="alert alert-danger">
        <?php echo($ERROR); ?>
    </div>
    <?php endif; ?>

    <?php if ($OKAY) : ?>
    <div class="alert alert-success">
        Account created! Welcome to <?php echo($CONFIG['company_name']); ?>
    </div>
    <?php endif; ?>

    <form method="post" action="signup.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Username">
        </div>
    
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full Name">
        </div>
        <div class="form-group">
            <label for="username">Password</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <button type="submit" class="btn btn-primary">Sign Up!</button>
    </form>

</main>

<?php include 'includes/footer.php' ?>