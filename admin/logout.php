<?php

session_start();
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] != "admin") {
    header("Location: /index.php");
}else{
header("Location:/admin/login.php");
}
session_unset();
session_destroy();






