
<?php

// Start the session if it has not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check whether a student is logged in
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {

        // Redirect to login page
        header("Location: ../login.php");
        exit();
    }
}

// Check whether the logged-in user is an administrator
function requireAdmin()
{
    requireLogin();

    if (!isset($_SESSION['user_role']) ||
        $_SESSION['user_role'] !== 'admin') {

        header("Location: ../dashboard.php");
        exit();
    }
}

?>
