<?php

class RoomFetchForWebsite
{

    protected static $conn;
    public static function initializeConnection()
    {
        if (self::$conn === null) {
            if (file_exists('../admin/dbConnect.php')) {
                self::$conn = require('../admin/dbConnect.php');
            } elseif (file_exists('admin/dbConnect.php')) {
                self::$conn = require('admin/dbConnect.php');
            }

            if (self::$conn === null) {
                die("Error: Database connection not established.");
            }
        }
    }
    public static function fetchRoomData($query)
    {
    
        self::initializeConnection();
       
        $result = self::$conn->query($query);
        if ($result->num_rows > 0) {
            $totalRooms = $result->num_rows;
            $rooms = [];
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
            if(empty($rooms)){
                return "No Room Found";
            }
            return $rooms;
        }
    }
    public static function fetchBookingData($query)
    {
        self::initializeConnection();
        $result = self::$conn->query($query);
        if ($result->num_rows > 0) {
           return [
                "success"=> false,
                "code"=> 400,                      // HTTP Status Code
                "message"=> "Already Room Booked ", // A short description of the error
               
                ];
              
        }else{
            return "No Booking Found";
        }       
    }
    public static function fetchExistingData($query)
    {
        self::initializeConnection();
        $result = self::$conn->query($query);
        if ($result->num_rows > 0) {
           return [
                "success"=> true,
                "code"=> 400,                      // HTTP Status Code
                "message"=> "Already User Booked Room ", // A short description of the error
               
                ];
              
        }else{
            return "No Booking Found";
        }       
    }
}