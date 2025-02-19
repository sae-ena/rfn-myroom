<?php
require "leftSidebar.php";
require "dbConnect.php";
 require('../helperFunction/InsertRoomData.php');
$totalRooms =0;
$rooms = [];
if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Fetch data from the ROOMS table
    $query = "SELECT   u.user_name,   u.user_email, u.user_number,  r.room_name,  r.room_image,   r.room_description,   r.room_price,   b.booking_date,   b.status, b.description, b.booking_id
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
        $totalRooms = count($rooms);
      
    }else{
        $queryRoom = "SELECT * from rooms where room_id = '$roomId';";
        $rs = mysqli_query($conn,$queryRoom);
        while($row = mysqli_fetch_assoc($rs)){
            $rooms[]= $row;
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
        echo '<script type="text/javascript">
        // Set localStorage to show modal
        localStorage.setItem("successModal", "true");
        // Redirect back to the referring page
        window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
      </script>';
    exit();// Stop further execution
    }elseif(isset($_POST['booking_cancel_id_admin'])) {

        $booking_id = $_POST['booking_cancel_id_admin'];
        // Update the status of the booking
        $status="canceled";
        $query = "UPDATE bookings SET status = '$status' WHERE booking_id = '$booking_id'";
        $sqlResult = InsertRoomData::insertData($query);
        $successfullyApprove = "Booking Cancelled successfully!";
        echo '<script type="text/javascript">
        // Set localStorage to show modal
        localStorage.setItem("successModal", "true");
        // Redirect back to the referring page
        window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
      </script>';
    exit();// Stop further execution
    }
}


?>
<?php if (isset($successfullyApprove)): ?>
    <div class="success-notify">
        <span><?php echo $successfullyApprove; ?></span>
    </div>
<?php endif;
if (is_array($rooms) && count($rooms) == 0) {
    // require('pageNotFound.php');
    // exit();
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
            //    $path = "http://localhost:8000/uploads/".$rooms[0]["room_image"];
                echo ' <div class="container" style="">
               <div class="bookingList-image">';
              
               if ($rooms[0]["room_image"] != null) {
                    echo '<div class="container" style="position: relative; width: 100%;">';
                    echo '<img src="' . $rooms[0]["room_image"] . '" alt="bookingRoom Image" style="width: 100%; height: auto; border-radius: 19px; border: 1px solid black; display: block; object-fit: cover;">';
                    echo '<h2 style="position: absolute; bottom: 20px; left: 20px; width: 52%; background-color: rgba(255, 107, 1, 0.8); color: black; text-align: center; font-size: 24px; font-family: \'Roboto\', sans-serif; text-transform: uppercase; letter-spacing: 2px; padding: 10px; box-shadow: 0 4px 12px rgba(20, 13, 13, 0.3); text-shadow: 2px 2px 3px rgb(238, 238, 238), -2px -2px 5px #ffffff, 2px -2px 5px #ffffff, -2px 2px 5px #ffffff;border-radius:36px">' . $rooms[0]['room_name'] . '</h2>';
                    echo '</div>';
                
                   echo '</div>';
               }echo ' </div>';

                
                echo "<p style='color:rgb(255, 255, 255); font-size: 22px; line-height: 1.6; text-align: center;font-family: \'Roboto\', sans-serif;'> <span style='color: black;''> Description: </span> " . $rooms[0]['room_description'] . "</p>";
                
                echo "<p style='color:rgb(251, 255, 28); font-size: 23px; text-align: center; margin-top: 10px; font-family: \'Roboto\', sans-serif;'><span style='color:rgb(255, 0, 0);text-shadow: 2px 2px 1px #ffffff, -2px -2px 5px #ffffff, 2px -2px 5px #ffffff, -2px 2px 5px #ffffff; letter-spacing: 2px; font-weight: bold; font-size: 26px;'>Price: </span>" . $rooms[0]['room_price'] . " per month</p>
                <br>";
                
                
                ?>
          
          <?php
            if (is_array($rooms) && $totalRooms > 0) {?>
                      <div class="booked-users">
                <h3 style="display:block;padding-left:20px">Users who have booked this room:</h3>

                <?php
                foreach ($rooms as $user) {
                    echo '
                                <div class="user-booking">
                                    <h3>User ID : ' . $user['booking_id'] . '</h3>
                                    <p><strong>Username:</strong>' . $user['user_name'] . '</p>
                                    <p><strong>Email:</strong> ' . $user['user_email'] . '</p>
                                    <p><strong>Phone Number:</strong> ' . $user['user_number'] . '</p>
                                    <p><strong>Booking Date:</strong> ' . $user['booking_date'] . '</p>
                                    ';
                                    if(isset($user['description']) && !empty($user['description'])){
                                        dd($user);
                                       echo '<p><strong>User Note :</strong> ' . $user['description'] . '</p>';
                                        
                                    }
                                    echo'<div style="display:grid;grid-template-columns:1fr 1fr">
                                    <form  action="' . $_SERVER['PHP_SELF'] . '" method="POST">
                                    <input type="hidden" name="booking_cancel_id_admin" value="' . $user["booking_id"] . '">
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
        <?php ?>

        <div id="successModal">
    <div class="modal-content">
        <h3 class="success-message">Success</h3>
        <hr>
        <h4 id="successMessage"></h4>
    </div>
</div>


        </div>



</div>

<script>
    window.onload = function() {
        if (localStorage.getItem("successModal") === "true") {
            document.getElementById("successModal").style.display = "block";
            document.getElementById("successMessage").innerHTML = "Room successfully Booked.";
            localStorage.removeItem("successModal");

            setTimeout(() => {
                document.getElementById("successModal").style.display = "none";
            }, 2000);
        }
    }
    </script>
