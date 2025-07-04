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
    global $conn;

    // Sanitize inputs
    $location = trim($location);
    $roomType = trim($roomType);

    if (!empty($location) && !empty($roomType)) {
        $query = "SELECT r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
                  LEFT JOIN bookings b ON r.room_id = b.room_id
                  WHERE (LOWER(r.room_location) LIKE LOWER('%$location%') AND LOWER(r.room_type) = LOWER('$roomType'))
                  AND r.room_status = 'active'
                  AND (b.status != 'confirmed' OR b.booking_id IS NULL) ORDER BY r.created_at DESC;";
    } elseif (!empty($location)) {
        $query = "SELECT  r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
        LEFT JOIN bookings b ON r.room_id = b.room_id
        WHERE LOWER(r.room_location) LIKE LOWER('%$location%')
        AND r.room_status = 'active'
        AND (b.status != 'confirmed' OR b.booking_id IS NULL)  ORDER BY r.created_at DESC;";
    } else {
        $query = "SELECT r.room_id, r.room_name, r.room_location, r.room_price, r.room_type, r.room_status, r.room_description, r.room_image, r.created_at , b.booking_id,b.user_id,b.is_active,b.status,b.booking_date FROM rooms r
              LEFT JOIN bookings b ON r.room_id = b.room_id
              WHERE LOWER(r.room_type) = LOWER('$roomType')
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
    return false;

}

function getAvailableSearchOptions() {
    global $conn;
    
    $options = [
        'locations' => [],
        'roomTypes' => []
    ];
    
    // Get unique locations
    $locationQuery = "SELECT DISTINCT room_location FROM rooms WHERE room_status = 'active' ORDER BY room_location";
    $locationResult = $conn->query($locationQuery);
    if ($locationResult->num_rows > 0) {
        while ($row = $locationResult->fetch_assoc()) {
            $options['locations'][] = $row['room_location'];
        }
    }
    
    // Get unique room types
    $typeQuery = "SELECT DISTINCT room_type FROM rooms WHERE room_status = 'active' ORDER BY room_type";
    $typeResult = $conn->query($typeQuery);
    if ($typeResult->num_rows > 0) {
        while ($row = $typeResult->fetch_assoc()) {
            $options['roomTypes'][] = $row['room_type'];
        }
    }
    
    return $options;
}

function dd($data) {
    var_dump($data);
    exit;
}
function getBackendSettingValue($key) {
    global $conn;
    
    $key = mysqli_real_escape_string($conn, $key);
    $query = "SELECT value FROM backend_settings WHERE `name` = '$key' AND status = 1 LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['value'];
    }
    
    return null; // Return null if the key does not exist
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace(basename($script), '', $script);
    return $protocol . $host . $path;
}