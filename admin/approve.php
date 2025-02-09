<?php
require "leftSidebar.php";
require "dbConnect.php";


// Fetch data from the ROOMS table
$query = "SELECT   u.user_name,   u.user_email, r.room_location,  r.room_name,  r.room_image,      r.room_price,   b.booking_date,   b.status, b.booking_id
        FROM 
            bookings b
        JOIN 
            users u ON b.user_id = u.user_id
        JOIN 
            rooms r ON b.room_id = r.room_id
        WHERE 
            r.room_status ='inActive' AND b.status = 'confirmed';";
$stmt = $conn->prepare($query); // "i" denotes integer type for user_id
$stmt->execute();
$result = $stmt->get_result();
$rooms = [];
if ($result->num_rows > 0) {
    // Store the result in an array (optional if you need to manipulate later)
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

// Close connection
// $stmt->close();

?>
<?php if (isset($successfullyApprove)): ?>
    <div class="success-notify">
        <span><?php echo $successfullyApprove; ?></span>
    </div>
<?php endif; ?>


<div class="dashboard-content">
    <h1 class="roomH1" style="color:white">All Booked Room Records</h1>
    <!-- Filter Section -->
    <div class="filter-section">
      
    </div>
   


            <?php
            if (is_array($rooms) && count($rooms) > 0) {
               echo' <table class="room-table">
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Room Image</th>
                        <th>Room Title</th>
                        <th>Location</th>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>Booked Date</th>
                        <th colspan="2" style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>';
            foreach ($rooms as $key => $room) {
                echo "<tr>
                                         <td>" . ++$key . "</td>
                                         <td> <img src='uploads/" . $room['room_image'] . "' alt='room image' style='width: 100px; height: 100px;'></td>
                                         <td>" . $room['room_name'] . "</td>
                                         <td>" . $room['room_location'] . "</td>
                                         <td>" . $room['user_name'] . "</td>
                                         <td>" . $room['user_email'] . "</td>
                                         <td>" . $room['booking_date'] . "</td>
                                         <td>
                                             <a href='/admin/approveView.php?booking_id=" . $room['booking_id'] . "'><button class='edit-button' style='left: 16px; top: 8px;'>View</button> </a>
                                             </td>
                                             <td>
                                             
                                         </td>
                                     </tr>";
            }
        }else{
            echo'
            <div class="filter-section">
        <form method="GET" action="">
            <div class="form-row">
               

               
                    <h1 style="color:white;font-family:cursive" class="heading">No Room Available</h1>

                
            </div>
        </form>
    </div>';
        }
            ?>

            <!-- More rows as needed -->
        </tbody>
    </table>
</div>
</div>
</div>

</div>
<script>

    <?php if (isset($successfullyDeleted)): ?>
        setTimeout(function() {
            window.location.href = 'userTable.php'; 
        },300);
        <?php endif; ?>
        </script>