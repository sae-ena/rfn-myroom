<?php

class InsertRoomData
{
    public static function insertData($query)
    {
        if(file_exists('../admin/dbConnect.php' )) require('../admin/dbConnect.php');
        if(file_exists('admin/dbConnect.php' )) require('admin/dbConnect.php');
      
        if ($conn->query($query) === TRUE) {
            return "Success! Your update was saved.";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $conn->close();
    }
}

// Add payment_status and payment_txn_id columns if not present
// This is for realistic payment tracking
// Example: payment_status ('paid', 'pending', 'failed'), payment_txn_id (transaction/reference id)
// You may need to run an ALTER TABLE migration if these columns do not exist yet.