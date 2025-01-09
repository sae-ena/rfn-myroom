<?php

class CheckLogin{
    public static function islogin(){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();        
        }
        // if($_SESSION['user_type'] === "user") header("Location:../index.php");
        if (! isset($_SESSION['user_email']) && ! isset($_SESSION['user_name'])) {
            header("Location: login.php");
            exit(); // Ensure no further code is executed after the redirection
        }
        
    }
}

