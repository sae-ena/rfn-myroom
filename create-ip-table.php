<?php
    $host = "mysql-29620eed-kidssujal-9bd8.j.aivencloud.com"; 
    $port = 18250; // your MySQL port 
    $username = "avnadmin"; // your MySQL username
    $password = "AVNS_1N9Dr_M5lJZIRxcd8gj"; 
    $dbname = "rf_db"; 

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to create the table
$sql = "CREATE TABLE ip_address_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Execute the query and check if the table is created
if ($conn->query($sql) === TRUE) {
    echo "Table 'ip_address_info' created successfully!";
} else {
    echo "Error creating table: " . $conn->error;
}

$ip_address = '27.34.66.15';
$status = 'active';
$username = 'super-admin';
$location = 'new baneshwor';
$created_at = date('Y-m-d H:i:s');  // Current timestamp

// SQL query to insert the data
$sql = "INSERT INTO ip_address_info (ip_address, username, location, created_at, status)
        VALUES ('$ip_address', '$username', '$location', '$created_at', '$status')";

// Execute the query and check for success
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
