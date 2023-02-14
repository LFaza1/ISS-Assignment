<?php
include_once __DIR__ .'/libs/csrf/csrfprotector.php';

// Initialise CSRFProtector library
csrfProtector::init();
include 'includes/header.php' ?>

<main class="container">

    <ul class="nav nav-pills nav-justified">
        <li><a href="page.php?page=about.php">About</a></li>
        <li><a href="page.php?page=accounts.php">Accounts</a></li>
        <li><a href="page.php?page=checks.php">Checks</a></li>
        <li><a href="page.php?page=loans.php">Loans</a></li>
    </ul>
    <section id="page-content">
                <?php
        //code Vulnarable to Directory Traversal Attack
        /* if (array_key_exists('page', $_GET)) {

            include "pages/" . $_GET['page'];
        } else {
            echo ("<div>Page does not exist</div>");
        }
        */

        //fixed code 
        $valid_pages = array('about.php', 'accounts.php', 'checks.php', 'loans.php');

        if (array_key_exists('page', $_GET) && in_array($_GET["page"], $valid_pages)) {
            include "pages/" . $_GET['page'];
        } else {
            echo ("<div>Page does not exist</div>");
        }
        ?>
        
    </section>

</main>

<?php include 'includes/footer.php' ?>
