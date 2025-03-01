<?php
// Including the database configuration file
try{
require_once '../admin/dbConnect.php';

$checkTable = $conn->query("SHOW TABLES LIKE 'form_feilds'");
    
    if($checkTable->num_rows > 0) {
        echo "Table 'form_feilds' already exists!";
    } else {
$sql = "CREATE TABLE IF NOT EXISTS form_fields (
    field_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,  -- Auto-increment primary key
    field_name VARCHAR(255) NOT NULL,  -- Field name column
    field_title VARCHAR(255) ,  -- Field title column
    field_status TINYINT(1) DEFAULT 1  -- Field status column
);";

    // Executing the query
    $conn->query($sql);

    if ($conn->error) {
        // Throwing an exception if any error occurs
        throw new Exception($conn->error);
    }
   
        echo "Table 'form_fields' created successfully!";
   
}
} catch (Exception $e) {
    // Handling any errors that occur
    echo "Error: " . $e->getMessage();
}

?>