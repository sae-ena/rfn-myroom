<?php
require "leftSidebar.php";
require "dbConnect.php";
// Fetch data from the ROOMS table
$query = "SELECT r.*
FROM rooms r
LEFT JOIN bookings b ON r.room_id = b.room_id AND b.status = 'pending'
WHERE r.room_status = 'active'
  AND b.is_active = 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $totalRooms = $result->num_rows;
    // Store the result in an array (optional if you need to manipulate later)
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
} 
?>

<div class="dashboard-content">
<header class="dashboard-header">
                <h1 style="" class="for-heading">Dashboard Overview</h1>
            </header>
            <div class="bookingRoomMain">
                <?php 
                if (! $result->num_rows == 0){
                foreach($rooms as $room){
                    echo '
        <div class="bookingRoom-box">
            <div class="bookingRoom-image">
                <span class="for-bookRequest">Booking Request</span>';
                if($room["room_image"] != null){
                    echo '<img src="uploads/'.$room["room_image"].'" alt="bookingRoom Image">';}
                else{
                 echo ' <img src="uploads/67630b72ab163_jGandhi.png" alt="bookingRoom Image">';
                }
            echo'</div>
            <div class="bookingRoom-details">

            <h4>Room ID :'.$room["room_id"].'</h4>
                <h2 class="bookingRoom-title">'.$room["room_name"].'</h2>
                <p class="bookingRoom-price">'.$room["room_price"].' / Month</p>
                <p class="bookingRoom-description"><span style="font-family:monos;font-size:18px;color:black">Room Type  : </span>'.$room["room_type"].'</p>
                <p class="bookingRoom-location"><span style="font-family:monos;font-size:18px;color:black"> Location:</span> '.$room["room_location"].'</p>
                <button class="book-bookingRoom">  <a href="bookingList.php?room_id='.$room["room_id"].'">View Booking</a></button>
            </div>
        </div>';
                }
            }else{
                echo '<h1 style="color:white;font-family:cursive" class="for-heading">No Room Available</h1>';
            }
                ?>
              
        </div>
  
</div>
</div>