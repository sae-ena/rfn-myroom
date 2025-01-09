<?php

class RoomFetchForWebsite
{
    public static function fetchRoomData($query)
    {
        require('admin/dbConnect.php');
        $result = $conn->query($query);
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
        require('admin/dbConnect.php');
        $result = $conn->query($query);
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
        require('admin/dbConnect.php');
        $result = $conn->query($query);
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