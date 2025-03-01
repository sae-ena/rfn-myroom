<?php
// Including the database configuration file

use function HelperFunction\addColumnToTable;

try{
require_once '../admin/dbConnect.php';
require_once '../helperFunction/alterTable.php';

$sql = "
    CREATE TABLE IF NOT EXISTS form_managers (
        form_id CHAR(36) DEFAULT (UUID()) PRIMARY KEY,  
        form_name VARCHAR(255) NOT NULL,  
        form_slug VARCHAR(255) NOT NULL,  
        description TEXT,  -- Description of the form
        field_detail JSON,  -- JSON to store dynamic field details
        background_color VARCHAR(27) DEFAULT NULL,  
        background_image VARCHAR(255) DEFAULT NULL,  
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Timestamp for updates
        status TINYINT(1) DEFAULT 1, 
        created_by int , 
        updated_by int,  
        FOREIGN KEY (created_by) REFERENCES users(user_id),  -- Assuming a 'users' table with UUID as user_id
        FOREIGN KEY (updated_by) REFERENCES users(user_id)  -- Assuming a 'users' table with UUID as user_id
    );
    ";

    // Executing the query
    $conn->query($sql);

    if ($conn->error) {
        // Throwing an exception if any error occurs
        throw new Exception($conn->error);
    }
    if ($conn->affected_rows > 0) {
        // Outputting the success message
        echo "Table 'form_manager' created successfully! \n";
    }else{
        echo "Table 'form_manager' already exists! \n";    
    }

    addColumnToTable("form_managers","background_color","VARCHAR",50);
    addColumnToTable("form_managers","background_image","VARCHAR");

} catch (Exception $e) {
    // Handling any errors that occur
    echo "Error: " . $e->getMessage();
}
?>