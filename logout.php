<?php
session_start();

// Check CSRF token to prevent logout CSRF attacks
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }
}


session_unset();
session_destroy();

// Redirect to login page
header("Location:index.php");
exit();
?>
