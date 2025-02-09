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