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
                // Return null instead of dying to allow graceful error handling
                return false;
            }
        }
        return true;
    }
    public static function fetchRoomData($query)
    {
        if (!self::initializeConnection()) {
            // Return empty array if database connection fails
            return [];
        }
       
        $result = self::$conn->query($query);
        if ($result && $result->num_rows > 0) {
            $totalRooms = $result->num_rows;
            $rooms = [];
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
            if(empty($rooms)){
                return [];
            }
            return $rooms;
        }
        return [];
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