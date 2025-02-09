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

function getRoomData($location="",$roomType){
    
    if($location=="" && $roomType == "") return false;
    require("admin/dbConnect.php");
    
    // Ensure the location is provided and not empty
    if (!empty($location)) {
        $query = "SELECT * FROM rooms WHERE (room_location LIKE '%$location%' OR room_type = '$roomType') AND room_status = 'active';";
    } else {
        $query = "SELECT * FROM rooms WHERE room_type = '$roomType' AND room_status = 'active';";
    }
    
    
    $result = mysqli_query($conn,$query);
    if(mysqli_num_rows($result)> 0){
        $room = [];
        while($row = mysqli_fetch_assoc($result)){
            $room[] = $row;

        }
        return $room;
    }
    error_log("No room match");
    return false;

}