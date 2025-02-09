<?php
require "leftSidebar.php";
require "dbConnect.php";
 require('../helperFunction/InsertRoomData.php');

$rooms = [];
if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Fetch data from the ROOMS table
    $query = "SELECT   u.user_name,   u.user_email,   r.room_name,  r.room_image,   r.room_description,   r.room_price,   b.booking_date,   b.status, b.description, b.booking_id
        FROM 
            bookings b
        JOIN 
            users u ON b.user_id = u.user_id
        JOIN 
            rooms r ON b.room_id = r.room_id
        WHERE 
            r.room_id = ? AND b.status = 'pending' AND b.is_active = 1;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $roomId);  // "i" denotes integer type for user_id
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Store the result in an array (optional if you need to manipulate later)

        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['booking_id'])) {


        $booking_id = $_POST['booking_id'];
        $status = "confirmed";
        $roomQuery = "SELECT room_id FROM bookings WHERE booking_id = ?";
        $stmtRoom = $conn->prepare($roomQuery);
        $stmtRoom->bind_param("i", $booking_id);
        $stmtRoom->execute();
        $roomId = $stmtRoom->get_result()->fetch_assoc()['room_id'];

        // Update the status of the room
        $roomStatus = "inActive";
        $query = "UPDATE rooms SET room_status = '$roomStatus' WHERE room_id = '$roomId'";
        $sqlResult = InsertRoomData::insertData($query);

        // Update the status of the booking
        $query = "UPDATE bookings SET status = '$status' WHERE booking_id = '$booking_id'";
        $sqlResult = InsertRoomData::insertData($query);

        $query2 = "UPDATE bookings SET status = 'canceled' WHERE status != '$status' AND room_id = '$roomId';";
        $sqlResult = InsertRoomData::insertData($query2);

    $successfullyApprove = "Booking approved successfully!";

    header("Location:approve.php");
    exit;
 
    }elseif(isset($_POST['booking_cancel_id'])) {

        $booking_id = $_POST['booking_cancel_id'];
        // Update the status of the booking
        $Inactive=0;
        $query = "UPDATE bookings SET is_active = '$Inactive' WHERE booking_id = '$booking_id'";
        $sqlResult = InsertRoomData::insertData($query);
        $successfullyApprove = "Booking Cancelled successfully!";
        echo ' <div class="success-notify" >' . $successfullyApprove . '</div>';

    // Wait for 3 seconds before redirecting
    echo '<script>setTimeout(function(){ window.location.href = "' . $_SERVER['HTTP_REFERER'] . '"; }, 3000);</script>';
    exit;
    }
}


?>
<?php if (isset($successfullyApprove)): ?>
    <div class="success-notify">
        <span><?php echo $successfullyApprove; ?></span>
    </div>
<?php endif;
if (is_array($rooms) && count($rooms) == 0) {
    require('pageNotFound.php');
    exit();
}

?>
<div class="dashboard-content">
    <header class="dashboard-header">
        <h1 style="" class="for-heading">Booking Request </h1>
    </header>


    <div class="container" style="">
        <style>
            
        </style>
        <div class="room-details">
            <?php
            if (is_array($rooms) && count($rooms) > 0) {
            //    $path = "http://localhost:8000/uploads/".$rooms[0]["room_image"];
                echo ' <div class="container" style="">
               <div class="bookingList-image">';
                if ($rooms[0]["room_image"] != null) {
                    echo '<img src="uploads/' . $rooms[0]["room_image"] . '" alt="bookingRoom Image" style="border: 1px solid black;
        border-radius: 19px; width: 100%; height=100% " >';
                } else {
                    echo ' <img src="uploads/67630b72ab163_jGandhi.png" alt="bookingRoom Image">';
                }echo ' </div>';

                echo "<h2 style='display:block;width:auto; background-color:rgb(111, 209, 50); color: black; border-radius: 19px; box-shadow: 0 4px 12px rgba(20, 13, 13, 0.3); text-transform: uppercase;outline: 1px solid white;text-shadow: 2px 2px 1pxrgb(230, 8, 8), -2px -2px 5px #ffffff, 2px -2px 5px #ffffff, -2px 2px 5px #ffffff; letter-spacing: 2px; font-size: 24px; margin-top: 50px; margin-bottom: 22px; padding: 20px 0; text-align: center;font-family: \'Roboto\', sans-serif;'>" . $rooms[0]['room_name'] . "</h2>";
                
                echo "<br>";
                
                echo "<p style='color:rgb(255, 255, 255); font-size: 22px; line-height: 1.6; text-align: center;font-family: \'Roboto\', sans-serif;'>Description: " . $rooms[0]['room_description'] . "</p>";
                
                echo "<p style='color:rgb(251, 255, 28); font-size: 23px; text-align: center; margin-top: 10px; font-family: \'Roboto\', sans-serif;'><span style='color:rgb(255, 0, 0);text-shadow: 2px 2px 1px #ffffff, -2px -2px 5px #ffffff, 2px -2px 5px #ffffff, -2px 2px 5px #ffffff; letter-spacing: 2px; font-weight: bold; font-size: 26px;'>Price: </span>" . $rooms[0]['room_price'] . " per month</p>
                <br>";
                
                
                ?>
          

            <!-- Booked Users Section -->
            <div class="booked-users">
                <h3 style="display:block;padding-left:20px">Users who have booked this room:</h3>

                <?php
                foreach ($rooms as $user) {
                    echo '
                                <div class="user-booking">
                                    <h3>UID : ' . $user['booking_id'] . '</h3>
                                    <p><strong>Username:</strong>' . $user['user_name'] . '</p>
                                    <p><strong>Email:</strong> ' . $user['user_email'] . '</p>
                                    <p><strong>Booking Date:</strong> ' . $user['booking_date'] . '</p>
                                    <p><strong>User Note :</strong> ' . $user['description'] . '</p>
                                    <p><strong>Status:</strong> ' . $user['status'] . '</p>
                                    <div style="display:grid;grid-template-columns:1fr 1fr">
                                    <form  action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                                    <input type="hidden" name="booking_cancel_id" value="' . $user["booking_id"] . '">
                                     <button class="book-bookingRoom" style="background-color:red">Reject Booking</button>
                                     </form>
                                    <form  action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                                    <input type="hidden" name="booking_id" value="' . $user["booking_id"] . '">
                                     <button class="book-bookingRoom">Approve Booking</button>
                                     </form>
                                    
                                     </div>
                                     </div>
                                     ';
                }

            }

            ?>
        </div>




        </div>



</div>

