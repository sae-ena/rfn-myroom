<?php
require('helperFunction/roomFetchForWebsite.php');
require('helperFunction/InsertRoomData.php');
$query = "SELECT r.* FROM rooms r Left join bookings b ON r.room_id = b.room_id  where (b.status != 'confirmed' OR b.booking_id IS NULL)  AND r.room_status = 'active' ORDER BY r.created_at DESC  LIMIT 15;";
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
  $form_error =  $bookingResult['message'];
}



}



foreach ($rooms as $room) {
  echo '<div class="container ">
        <div class="card col-md-5 col-10">
          <img
            src="admin/uploads/' . $room["room_image"] . '"
            class="card-img-top"  width="100%"
            height="50%"
            alt="..."
          />
          <div class="card-body">
            <h5 class="TitleRoomDetails">' . $room["room_name"] . '</h5>
            <div class="card-text">
              <div class="desc">
                <p>
                ' . substr($room["room_description"], 0, 190) . '..
                </p>

                <p>
                  Price : RS

                 <b style="letter-spacing: 0.6px;"> ' . $room["room_price"] . '</b>/Month
                </p>

                <p class="locationRoomDetails">
                  <b style="color:black;">Location : </b> <i class="fa-solid fa-star">' . $room["room_location"] . ' </i>
                </p>
              </div>
            </div>
            ';
            if(isset($_SESSION['auth_id'])){
            echo'<div class="text-center">
            <form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
              <input type="hidden" name="room_id" value="' . $room["room_id"] . '">
              <button class="btn btn-warning w-50" style="color:white">
                Book Now
              </button>
              </form>
              </div>';
            }
            echo'
          </div>
        </div>


      </div>
    ';

}
