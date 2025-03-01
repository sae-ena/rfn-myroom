<?php


$host = "mysql-29620eed-kidssujal-9bd8.j.aivencloud.com"; // or your host IP
$port = 18250; // your MySQL port (default is 3306)
$username = "avnadmin"; // your MySQL username
$password = "AVNS_1N9Dr_M5lJZIRxcd8gj"; // your MySQL password
$dbname = "rf_db"; // your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

return $conn;
?>
