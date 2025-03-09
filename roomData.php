<?php
require('helperFunction/RoomFetchForWebsite.php');
require('helperFunction/InsertRoomData.php');


if (isset($_POST['room_id']) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
  $auth_id = $_SESSION['auth_id'];
  $room_id = $_POST['room_id'];
  $time = date("Y-m-d H:i:s");
  $remarks = $_POST['remarks'] ??null;

  
  $query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 1 ;";
  $bookingResult = RoomFetchForWebsite::fetchBookingData($query);
  if ($bookingResult == "No Booking Found") {
      // $successfullyRoomAdded = "Room Booked Successfully";
      $check_query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 0 ";
      $existingBooking = RoomFetchForWebsite::fetchExistingData($check_query);
      
      
      // If it exists, update the booking_date
      if (is_array($existingBooking)) {
        if(isset($remarks) && is_string($remarks)){
            $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, description = '$remarks' WHERE user_id = '$auth_id' AND room_id = '$room_id'";

        }else{

            $query = "UPDATE bookings SET booking_date = '$time', is_active = 1 WHERE user_id = '$auth_id' AND room_id = '$room_id'";
        }
          // Update the existing record
        } else {
            // Insert a new record
            $query = "INSERT INTO bookings (user_id, room_id, description, booking_date) VALUES ('$auth_id', '$room_id','$remarks', '$time')";
        }
        
    $bookingResult1 = InsertRoomData::insertData($query);
    if (strpos($_SERVER['HTTP_REFERER'], 'booking_details.php?booking_id=') !== false) {
      
    echo '<script type="text/javascript">
    // Set localStorage to show modal
    localStorage.setItem("showModalRoomAdded", "true");
    // Redirect back to the referring page
    window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
  </script>';
exit();// Stop further execution
    }
    $successfullyRoomAdded = $bookingResult1;
    $auth_id =
  $room_id = 
  $time = 
  $remarks = "";

  } else {
    $form_error = $bookingResult['message'];
  }

}
if(isset($_SESSION['auth_id']) && $_SESSION['user_type'] == "user"){
 $userID = $_SESSION['auth_id'];
    $query = "SELECT r.room_id,r.room_status,r.room_location,  r.room_name, r.room_image, b.is_active,r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id ,b.user_id FROM  rooms r LEFT JOIN  bookings b ON b.room_id = r.room_id AND b.user_id = '$userID'  WHERE ( r.room_status ='active') ORDER BY r.created_at DESC  LIMIT 26;";
}else{

    $query = "SELECT  r.room_id,r.room_status,  r.room_name,r.room_location, r.room_image, b.is_active,r.room_description, r.room_price, b.booking_date, b.status, b.description, b.booking_id FROM rooms r Left join bookings b ON r.room_id = b.room_id  where (b.status != 'confirmed' OR b.booking_id IS NULL)  AND r.room_status = 'active' ORDER BY r.created_at DESC  LIMIT 26;";
}
$rooms = RoomFetchForWebsite::fetchRoomData($query);



if (isset($searchResult) && is_array($searchResult)) {
  ?>
  <h4  style="text-align: center; font-size: 33px; font-weight: bold; color: #333; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; padding: 10px 0; background-color: #f1f1f1; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);" id="roomsTitleSearch">Search Rooms  </h4>
  
  <div class="Rooms-12">
  <?php
  foreach ($searchResult as $room) {
    

      // Static unique ID set to 12 for all rooms
      echo '<div class="room-container-12">';
       
      if(isset($_SESSION['auth_id']) && $_SESSION['auth_id']){

          if(($_SESSION['auth_id'] == $room['user_id'] ) && ($room['status'] === "pending" && $room['is_active'] == true)){
              echo' <div style="position: absolute; top: 0; right: 0; background-color: rgba(0, 255, 47, 0.97); color: white; padding: 7px; z-index: 1; border-bottom-left-radius: 22px;">
              
              <strong>   BOOKED</strong>
              </div>';
            }
        }
    if((isset($_SESSION['auth_id']) && $_SESSION['user_type'] == "user" ) && ($room['status'] === "canceled" && $room['is_active'] == true)){
        echo' <div style="position: absolute; top: 0; right: 0; background-color: rgba(255, 0, 0, 0.8); color: white; padding: 5px; z-index: 1; border-bottom-left-radius: 22px; padding-left: 20px; padding-right: 20px;">

        <strong>   REJECTED</strong>
</div>';}

              echo'<div class="room-card-12">
                  <div class="room-image-container-12">
                      <img src="admin/'.$room["room_image"] . '" class="room-image-12" alt="Room image"/>
                  </div>
                  <div class="room-details-12">
                      <h5 class="room-title-12">' . $room["room_name"] . '</h5>
                      <div class="room-description-12">
                          <p>' . substr($room["room_description"], 0, 190) . '...</p>
                      </div>
                      <div class="room-price-location-12">
                          <span class="room-price-12">RS <b>' . $room["room_price"] . '</b>/Month</span>
                          <span class="room-location-12"><i class="fa-solid fa-location-dot"></i> ' . $room["room_location"] . '</span>
                      </div>
                      <div class="room-book-buttons-containerSearch">
                      <div class="room-view-button">
                          <form action="booking_details.php" method="GET">
                              <input type="hidden" name="booking_id" value="';?><?php echo $room['room_id']; ?>">
                              <button class="view-btn-12">View Room</button>
                          </form>
                      </div>
  
                      <?php  if (isset($_SESSION['auth_id']) && ($_SESSION['auth_id'] != $room['user_id']) && ( isset($room['is_active']) || $room['is_active'] == true)) : ?>
                          <div class="room-book-now-button-12">
                              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                  <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                  <button class="book-now-btn-12">Book Now</button>
                              </form>
                          </div>
                      <?php endif; ?>
                  </div>
                <?php  
                  echo '</div>
              </div>
          </div>';
  }
  echo '</div>';
}
?>

<h4 class="roomsTitle" id="roomsTitle">Explore Rooms
    <?php if(! isset($_SESSION['auth_id'])){  
    echo'<span style="position:relative;left:691px; font-size:16px;padding: 10px 30px;background-color:rgb(219, 57, 57); border-radius:36px 12px">Please log in to book a room.</span>';} ?>
</h4>
</div>
<div class="container">
    <div class="gallery-grid">
        <?php foreach ($rooms as $room) : ?>
            <?php
            $currentRoomId = $room['room_id'] ;

            if (isset($previousRoomId) && $previousRoomId == $currentRoomId) {
                continue;
            }

            ?>
           
            <div class="gallery-item1">
                <?php 
               
                if(((isset($_SESSION['auth_id']) ) && ($_SESSION['auth_id']== $room['user_id']) && $_SESSION['user_type'] == "user" ) && ($room['status'] === "pending" && $room['is_active'] == true)){
                   echo' <div style="position: absolute; top: 0; right: 0; background-color: rgba(0, 255, 47, 0.97); color: white; padding: 7px; z-index: 1; border-bottom-left-radius: 22px;">
      
                   <strong>   BOOKED</strong>
    </div>';

                }?>
                 <?php 
                if((isset($_SESSION['auth_id']) && $_SESSION['user_type'] == "user" ) && ($room['status'] === "canceled" && $room['is_active'] == true)){
                   echo' <div style="position: absolute; top: 0; right: 0; background-color: rgba(255, 0, 0, 0.97); color: white; padding: 5px; z-index: 1; border-bottom-left-radius: 22px;">
      
                   <strong>   REJECTED</strong>
    </div>';

                }?>
                    <div class="image-container">
                        <img
                            src="admin/<?php echo $room['room_image']; ?>"
                            class="custom-room-image"
                            alt="Room image"
                        />
                        <!-- Hidden info block, revealed on hover -->
                        <div class="custom-room-info">
                            <div class="card-text">
                                <div class="desc" style=" border-radius: 31px 22px 12px 10px;">
                                    <div style="height: 140px;">
                                        <p style="color:rgb(0, 0, 0);"><?php echo substr($room["room_description"], 0, 190); ?>...</p>
                                    </div>
                                        
                                        <span class="custom-location-room-details" style="color:rgb(0, 0, 0); font-size: 16px; font-weight: 500;margin-bottom: 8px;">
                                        Location:
                                    </span>
                                    <b style="color:rgb(0, 0, 0);"><?php echo $room["room_location"]; ?></b>
                                        <span class="custom-price" style="display:block">Price: RS <?php echo $room["room_price"]; ?>/Month</span>
                                    
                                </div>
                            </div>
            
                            <!-- Buttons container: View Room and Book Now -->
                            <div class="room-book-buttons-containerMain">
                                <div class="room-view-button">
                                    <form action="booking_details.php" method="GET">
                                        <input type="hidden" name="booking_id" value="<?php  echo $room['room_id']; ?>">
                                        <button class="view-btn-12" type="submit">View Room</button>
                                    </form>
                                </div>
            
                                <?php if (isset($_SESSION['auth_id']) && (! isset($room['is_active']) || $room['is_active'] == false)) : ?>
                                    <div class="room-book-now-button-12">
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                            <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                            <button class="book-now-btn-12" type="submit"> Book Now</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="background: linear-gradient(to right,rgb(248, 125, 10), #8f94fb); color: #fff; border-radius: 0px 0px 10px 10px;  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <h5 class="custom-title-room-details" style="font-family: 'Arial', sans-serif; font-weight: 600; font-size: 1.5rem; text-align: center; text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.2);">
        <?php echo $room["room_name"]; ?>
    </h5>
</div>

                </div>
                <?php
            $previousRoomId = $room['room_id'] ;

            ?>
              
            <?php endforeach; ?>
            <!-- End of image-container -->
                        </div>
                        
                    </div> <!-- End of gallery-item1 -->
               <?php
          
        ?>
    </div>
</div>
