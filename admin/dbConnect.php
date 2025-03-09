<?php
    $host = "mysql-29620eed-kidssujal-9bd8.j.aivencloud.com"; 
    $port = 18250; // your MySQL port 
    $username = "avnadmin"; // your MySQL username
    $password = "AVNS_1N9Dr_M5lJZIRxcd8gj"; 
    $dbname = "rf_db"; 


    $conn = new mysqli($host, $username, $password, $dbname, $port);

// Create a connection

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

return $conn;
?>
