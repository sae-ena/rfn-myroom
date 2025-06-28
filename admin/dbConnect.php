<?php
    $host = "mysql-29620eed-kidssujal-9bd8.j.aivencloud.com"; 
    $port = 18250; // your MySQL port 
    $username = "avnadmin"; // your MySQL username
    $password = "AVNS_1N9Dr_M5lJZIRxcd8gj"; 
    $dbname = "rf_db"; 

    // Suppress error reporting for database connection
    error_reporting(0);
    
    try {
        $conn = new mysqli($host, $username, $password, $dbname, $port);
        
        // Check the connection
        if ($conn->connect_error) {
            // Log error internally but don't expose details to user
            error_log("Database connection failed: " . $conn->connect_error);
            return null;
        }
        
        // Re-enable error reporting after successful connection
        error_reporting(E_ALL);
        return $conn;
        
    } catch (Exception $e) {
        // Log error internally but don't expose details to user
        error_log("Database connection exception: " . $e->getMessage());
        error_reporting(E_ALL);
       die();
    }
?>
