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


            if (array_key_exists('page', $_GET) ) {
                switch ($_GET['page']) {
                    case 'about.php':
                        include 'pages/about.php';
                        break;
                    case 'accounts.php':
                        include 'pages/accounts.php';
                        break;
                    case 'checks.php':
                        include 'pages/checks.php';
                        break;
                    case 'loans.php':
                        include 'pages/loans.php';
                        break;
                }
            } else {
                echo("<div>Page does not exist</div>");
            }

        ?>
    </section>

</main>

<?php include 'includes/footer.php' ?>