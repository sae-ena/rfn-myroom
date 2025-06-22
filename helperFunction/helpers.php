<?php
function convertToNullIfEmpty($input) {
    // Check if the input is an empty string (""), and return null if true
    if ($input === "") {
        return null;
    }
    
    // If the string has only 1 character, return the string as is
    if (strlen($input) === 1) {
        return $input;
    }
    
    // Otherwise, return the string unchanged
    return $input;
}

function getRoomData($roomType, $location = "") {
    if ($location == "" && $roomType == "") return false;
    require("admin/dbConnect.php");

    if (!empty($location) && !empty($roomType)) {
        $query = "SELECT r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
                  LEFT JOIN bookings b ON r.room_id = b.room_id
                  WHERE (r.room_location LIKE '%$location%' AND r.room_type = '$roomType')
                  AND r.room_status = 'active'
                  AND (b.status != 'confirmed' OR b.booking_id IS NULL) ORDER BY r.created_at DESC;";
    } elseif (!empty($location)) {
        $query = "SELECT  r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
        LEFT JOIN bookings b ON r.room_id = b.room_id
        WHERE r.room_location LIKE '%$location%'
        AND r.room_status = 'active'
        AND (b.status != 'confirmed' OR b.booking_id IS NULL)  ORDER BY r.created_at DESC;";
    } else {
        $query = "SELECT r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
              LEFT JOIN bookings b ON r.room_id = b.room_id
              WHERE r.room_type = '$roomType'
              AND r.room_status = 'active'
              AND (b.status != 'confirmed' OR b.booking_id IS NULL)  ORDER BY r.created_at DESC;";
    }
    
    $result = mysqli_query($conn,$query);
    if(mysqli_num_rows($result)> 0){
        $room = [];
        $previousRoomID =0;
        while($row = mysqli_fetch_assoc($result)){
           
            if($previousRoomID === $row['room_id']){
                
            }else{
            $room[] = $row;
        }
            $previousRoomID = $row['room_id'];

        }

        return $room;
    }
    error_log("No room match");
    return false;

}
function dd($data) {
    var_dump($data);
    exit;
}