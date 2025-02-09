<?php
require('helperFunction/roomFetchForWebsite.php');
require('helperFunction/InsertRoomData.php');
$query = "SELECT  r.* FROM rooms r Left join bookings b ON r.room_id = b.room_id  where (b.status != 'confirmed' OR b.booking_id IS NULL)  AND r.room_status = 'active'GROUP BY r.room_id ORDER BY r.created_at DESC  LIMIT 15;";
$rooms = RoomFetchForWebsite::fetchRoomData($query);


if (isset($_POST['room_id']) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {

  $auth_id = $_SESSION['auth_id'];
  $room_id = $_POST['room_id'];
  $time = date("Y-m-d H:i:s");


  $query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 1 ;";
  $bookingResult = RoomFetchForWebsite::fetchBookingData($query);
  if ($bookingResult == "No Booking Found") {
    // $successfullyRoomAdded = "Room Booked Successfully";
    $check_query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 0 ";
    $existingBooking = RoomFetchForWebsite::fetchExistingData($check_query);


    // If it exists, update the booking_date
    if (is_array($existingBooking)) {
      // Update the existing record
      $query = "UPDATE bookings SET booking_date = '$time', is_active = 1 WHERE user_id = '$auth_id' AND room_id = '$room_id'";
    } else {
      // Insert a new record
      $query = "INSERT INTO bookings (user_id, room_id, booking_date) VALUES ('$auth_id', '$room_id', '$time')";
    }
    $bookingResult1 = InsertRoomData::insertData($query);

    $successfullyRoomAdded = $bookingResult1;

  } else {
    $form_error = $bookingResult['message'];
  }



}


if (isset($searchResult) && is_array($searchResult)) {
  ?>
  <h4  style="text-align: center; font-size: 33px; font-weight: bold; color: #333; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; padding: 10px 0; background-color: #f1f1f1; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);" id="roomsTitleSearch">Search Rooms  </h4>
  
  <div class="Rooms-12">
  <?php
  foreach ($searchResult as $room) {
      // Static unique ID set to 12 for all rooms
      echo '<div class="room-container-12">
              <div class="room-card-12">
                  <div class="room-image-container-12">
                      <img src="admin/uploads/' . $room["room_image"] . '" class="room-image-12" alt="Room image"/>
                  </div>
                  <div class="room-details-12">
                      <h5 class="room-title-12">' . $room["room_name"] . '</h5>
                      <div class="room-description-12">
                          <p>' . substr($room["room_description"], 0, 190) . '...</p>
                      </div>
                      <div class="room-price-location-12">
                          <span class="room-price-12">RS <b>' . $room["room_price"] . '</b>/Month</span>
                          <span class="room-location-12"><i class="fa-solid fa-location-dot"></i> ' . $room["room_location"] . '</span>
                      </div>';
                      if (isset($_SESSION['auth_id'])) {
                          echo '<div class="room-book-button-12">
                                  <form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                                      <input type="hidden" name="room_id" value="' . $room["room_id"] . '">
                                      <button class="book-now-btn-12">Book Now</button>
                                  </form>
                              </div>';
                      }
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
</div><div class="container">
    <div class="gallery-grid">
        <?php
            foreach ($rooms as $room) {
                echo '<div class="gallery-item1">
                        <div class="image-container">
                            <img
                                src="admin/uploads/' . $room["room_image"] . '"
                                class="custom-room-image"
                                alt="Room image"
                            />
                            <!-- Hidden info block, revealed on hover -->
                            <div class="custom-room-info">
                                <div class="card-text">
                                    <div class="desc">
                                        <p style="color:rgb(0, 0, 0)" ;>' . substr($room["room_description"], 0, 190) . '...</p>
                                        <!-- Price and location as spans -->
                                        <b class="custom-price" style="color: #000000; font-size: 18px; font-weight: bold; margin-top: 6px;">Price:  RS ' . $room["room_price"] . '/Month</b>
                                        <br>
                                        <span class="custom-location-room-details" style="color:rgb(0, 0, 0); font-size: 16px; font-weight: 500;margin-bottom: 8px;">Location:
                                        </span>
                                            <b style="color:rgb(0, 0, 0)">' . $room["room_location"] . '</b>
                                    </div>
                                </div>';
                                if (isset($_SESSION['auth_id'])) {
                                    echo '<div class="text-center">
                                            <form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                                              <input type="hidden" name="room_id" value="' . $room["room_id"] . '">
                                              <button class="btn btn-warning w-50" style="color:white">Book Now</button>
                                            </form>
                                          </div>';
                                }
                            echo '</div> <!-- End of custom-room-info --> 
                        </div> <!-- End of image-container -->
                        <div class="card-body">
                            <h5 class="custom-title-room-details">' . $room["room_name"] . '</h5>
                        </div>
                    </div> <!-- End of gallery-item1 -->
                ';
            }
        ?>
    </div>
</div>
