<?php

class InsertRoomData
{
    public static function insertData($query)
    {
        require('admin/dbConnect.php');
        if ($conn->query($query) === TRUE) {
            return "New record created successfully";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $conn->close();
    }
}