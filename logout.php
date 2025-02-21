<?php
session_start();

// Check CSRF token to prevent logout CSRF attacks
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo $_POST['csrf_token'];
    echo $_SESSION['csrf_token'];
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }
}

// Unset all session variables
$_SESSION = array();

// Delete session cookie securely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: index.php");
exit;
?>
