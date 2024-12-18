<?php
require "leftSidebar.php";
require "dbConnect.php";  // Make sure this file contains the correct database connection

$query = "SELECT * from rooms where room_status = 'active';";
$result = $conn->query($query);

if(!$result) exit("Connection failed to fetch Data");

//Delete Operation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
   $roomId = $_POST['roomUid'];
    $query = "UPDATE rooms SET room_status = 'inActive' WHERE room_id = $roomId;";
   $deleteResult = $conn->query($query);

   if(!$deleteResult) exit("Connection failed to fetch Data");

   $successfullyDeleted = "RoomID : ".$roomId." has been deleted";

}



 ?>  
 
 <?php if (isset($successfullyDeleted)): ?>
            <div class="success-notify">
                <span><?php echo $successfullyDeleted; ?></span>
            </div>
        <?php endif; ?>
    <div class="dashboard-content">
        <h1 class="roomH1">All Active Rooms</h1>
        <table class="room-table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Room Title</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Room Type</th>
                    <th>Status</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
           <?php if($result->num_rows >0){
            while($room = $result->fetch_assoc()){
               echo"<tr>
                    <td>".$room['room_id']."</td>
                    <td>".$room['room_name']."</td>
                    <td>".$room['room_location']."</td>
                    <td>".$room['room_price']."/month</td>
                    <td>".$room['room_type']."</td>
                    <td>".$room['room_status']."</td>
                    <td>Unbooked</td>
                    <td>
                        <a href='/admin/form.php?id=".$room['room_id']."'><button class='edit-button'>Edit</button> </a>
                        <form action=".  $_SERVER['PHP_SELF']." method='POST' >
                        <input type='number' hidden value=".$room['room_id']." name='roomUid' /> 
                        <button type='submit' class='delete-button'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
                
   
}?>


              
                <!-- More rows as needed -->
            </tbody>
        </table>
    </div>
    </div>
</body>
<script>

    <?php if (isset($successfullyDeleted)): ?>
        setTimeout(function() {
            window.location.href = 'roomTable.php'; 
        },1000);
        <?php endif; ?>
        </script>
</html>
