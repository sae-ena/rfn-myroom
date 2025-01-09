<?php
if (!isset($_SESSION)) {
    session_start();
}
if ($_SESSION['user_type'] === "user" && isset($_SESSION['auth_id']))
    return;
header('Location:index.php');
