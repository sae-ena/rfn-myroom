<?php
require "leftSidebar.php";
require "dbConnect.php";


if(isset($_GET['booking_id']) && $_SERVER['REQUEST_METHOD'] === 'GET'){
    $booking_id = $_GET['booking_id'];

// Fetch data from the ROOMS table
$query = "SELECT   u.user_name,   u.user_email, r.room_location, r.room_description,  r.room_name,  r.room_image,      r.room_price,   b.booking_date,   b.status, b.booking_id
        FROM 
            bookings b
        JOIN 
            users u ON b.user_id = u.user_id
        JOIN 
            rooms r ON b.room_id = r.room_id
        WHERE 
            b.booking_id = $booking_id;";
$stmt = $conn->prepare($query); 
$stmt->execute();
$result = $stmt->get_result();
echo $result->num_rows;

if ($result->num_rows > 0) {
    // Store the result in an array (optional if you need to manipulate later)
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}
}

?>

<div class="dashboard-content">
<header class="dashboard-header">
                <h1 style="color:white;font-family:cursive" class="">Approved Booking List </h1>
            </header>
            <div class="booked-users">

          
                    <?php
                    if(! $result->num_rows == 0){
                            foreach($rooms as $key => $user){
                                
                   echo' 
                   <div class="user-booking">
                   <h1 style="" class="for-approveheading">'. $user['room_name'].' </h1> 
                                
                              <div class="room-details">
                              
                                 <div class="approve-image">';
                                 if($user["room_image"] != null){
                                     echo '<img src="'.$user["room_image"].'" alt="bookingRoom Image" style="width:100%";height:auto;object-fit:cover;>';}
                                 else{
                                  echo ' <img src="uploads/67630b72ab163_jGandhi.png" alt="bookingRoom Image">';
                                 }
                            echo' </div>
    
                            <span style="font-size: 18px; font-weight: bold; color: #FF5722; background-color: #FFF3E0; padding: 5px 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
RS '. $user['room_price'].' /Month
</span>
</div>
<h3 style="font-size: 22px; font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 2px solid #FF5722; padding-bottom: 5px;">
   Description
</h3>

<p style="font-size: 16px; color: #555; line-height: 1.6; margin-bottom: 20px; text-align: justify; background-color: #FAFAFA; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">.'. $user["room_description"].'
</p>
            
                       
                       <div class="booked-users" style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 20px auto;">
    <h3 style="font-size: 24px; font-weight: 700; color: #2C3E50; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; position: relative; padding-bottom: 10px;">
        Users who have confirmed this room:
        <span style="position: absolute; width: 60px; height: 3px; background-color: #FF5733; bottom: 0; left: 0;"></span>
    </h3>

    <div class="user-booking" style="background-color: #ffffff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 15px;">
        <h3 style="font-size: 20px; font-weight: 600; color: #34495E; margin-bottom: 10px;">
            UID: <span style="color: #FF5733;"> '.$user['booking_id'].' </span>
        </h3>
        <p style="font-size: 16px; color: #34495E; margin: 5px 0;">
            <strong style="color: #FF5733;">Username:</strong> '. $user['user_name'].'
        </p>
        <p style="font-size: 16px; color: #34495E; margin: 5px 0;">
            <strong style="color: #FF5733;">Email:</strong> '. $user['user_email'].'
        </p>
        <p style="font-size: 16px; color: #34495E; margin: 5px 0;">
            <strong style="color: #FF5733;">Booking Date:</strong> '. $user['booking_date'].'
        </p>

        <button class="book-bookingRoom" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">
            Approved
        </button>
    </div>
</div>

        </div>
                                ';
                            }
                        }
                        else{
                            require('pageNotFound.php');
                        }
                            ?>
                        </div>
              
                
       
  
