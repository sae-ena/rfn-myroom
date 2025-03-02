<?php

class CheckLogin{
    public static function islogin(){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();        
        }

      
        // if($_SESSION['user_type'] === "user") header("Location:../index.php");
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== "admin") {
            header("Location: ../index.php");
            exit(); 
        }
        elseif (! isset($_SESSION['user_type']) && ! isset($_SESSION['user_email'])) {
            header("Location: /admin/login.php");
            exit(); 
        }
       
        
    }
}

