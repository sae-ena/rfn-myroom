<?php
require "leftSidebar.php";
require "dbConnect.php";

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
        $query = "UPDATE rooms SET room_status = ? WHERE room_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $roomStatus, $roomId);
        $stmt->execute();

        // Update the status of the booking
        $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();

        $query2 = "UPDATE bookings SET status = ? WHERE status != ? AND room_id = ? ";
        $stmt2 = $conn->prepare($query2);
        $cancelStatus = "canceled";
        // $stmt->bind_param("sis", $cancelStatus, $booking_id, $status)
        $stmt2->bind_param('ssi',$cancelStatus,$status,$roomId);
        $stmt2->execute();
       

    $successfullyApprove = "Booking approved successfully!";
 
    }elseif(isset($_POST['booking_cancel_id'])) {

        $booking_id = $_POST['booking_cancel_id'];
        // Update the status of the booking
        $query = "UPDATE bookings SET is_active = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $Inactive=0;
        $stmt->bind_param("si", $Inactive, $booking_id);
        $stmt->execute();

        $successfullyApprove = "Booking Cancelled successfully!";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
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
                }
                echo ' </div>';

                echo "<h2>Room Name:" . $rooms[0]['room_name'] . "</h2>
                            <p>Description: " . $rooms[0]['room_description'] . "</p>
                            <p>Price: " . $rooms[0]['room_price'] . " per month</p>";
                ?>
          

            <!-- Booked Users Section -->
            <div class="booked-users">
                <h3>Users who have booked this room:</h3>

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

