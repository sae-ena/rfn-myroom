<?php

class InsertRoomData
{
    public static function insertData($query)
    {
        require('admin/dbConnect.php');
        if ($conn->query($query) === TRUE) {
            return "Success! Your update was saved.";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $conn->close();
    }
}