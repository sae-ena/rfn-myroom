<?php
require "leftSidebar.php";
require "dbConnect.php";  // Make sure this file contains the correct database connection

$query = "SELECT * from rooms where room_status = 'active';";
$result = $conn->query($query);

if(!$result) exit("Connection failed to fetch Data");

//Delete Operation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
   $roomId = $_POST['roomUid'];
   $query = "DELETE from rooms where room_id= '$roomId' AND room_status = 'inActive';";
   $deleteResult = $conn->query($query);
   
   
   if($conn->affected_rows > 0){
       
       $successfullyDeleted = "RoomID : ".$roomId." has been deleted";
    }else{
        $query = "UPDATE rooms SET room_status = 'inActive' WHERE room_id = $roomId;";
        $updateResult = $conn->query($query);

        if($updateResult) $successfullyDeleted = "RoomID : ".$roomId." status changed";
        

   }

   if(!$deleteResult) exit("Connection failed to fetch Data");


}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
    $userId = $_POST['statusChange'];
    $userCurrentStatus = $_POST['statusValue'];
    if($userCurrentStatus == 'active') $userCurrentStatus = 'inActive';
    else $userCurrentStatus = 'active';
     $query = "UPDATE rooms SET room_status = '$userCurrentStatus' WHERE room_id = $userId;";
    $deleteResult = $conn->query($query);
 
    if(!$deleteResult) exit("Connection failed to fetch Data");
 
    $successfullyDeleted = "Room ID : ".$userId." status updated to ".$userCurrentStatus;
 
 }
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['status']) )) {
    
   $status = $_GET['status']?? "";
   $roomId = $_GET['search']?? "";
 
   if(isset($status) && is_numeric($roomId)){
       $query = "SELECT * from rooms where room_status = '$status' AND room_id = '$roomId';";
    }elseif(isset($status) || isset($roomId)){
        $query = "SELECT * from rooms where room_status = '$status' OR room_id = '$roomId';";
     }
    $result = $conn->query($query);

   if(!$result) exit("Connection failed to fetch Data");

}



 ?>  
 
 <?php if (isset($successfullyDeleted)): ?>
            <div class="success-notify">
                <span><?php echo $successfullyDeleted; ?></span>
            </div>
        <?php endif; ?>
        <div class="dashboard-content"> 
        <h1 class="roomH1" style="color:white">All Room Records</h1>
     <!-- Filter Section -->
     <div class="filter-section">
        <form method="GET" action="">
            <div class="form-row">
                <!-- Status Filter -->
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="" disabled>---</option>
                    <option value="active" <?php echo isset($status) && $status == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inActive" <?php echo isset($status) && $status == 'inActive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <!-- Search Filter -->
                <label for="search">Search:</label>
                <input type="number" name="search" id="search" value="<?php echo isset($roomId)?  $roomId:"" ?>" placeholder="Room UID...">

                <!-- Filter Button -->
               
                <button type="submit">Filter</button>
            </div>
        </form>
    </div>
        <table class="room-table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Room Title</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Room Type</th>
                    <th>Status</th>
                    <th colspan="2" style="text-align: center;">Action</th>
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
                    <td>
                     <form action=".  $_SERVER['PHP_SELF']." method='POST' >
                     <input type='text' hidden value=".$room['room_id']." name='statusChange' />
                     <input type='text' hidden value=".$room['room_status']." name='statusValue' />
                    ";
                    if($room['room_status'] =='active') echo "<button class='edit-button' style=''>Active</button>";
                    else echo "<button class='delete-button'>InActive</button>";
                    echo
                    "
                       </form>
                       </td>
                    <td>
                        <a href='/admin/form.php?id=".$room['room_id']."'><button class='edit-button' style='left: 16px; top: 8px;'>Edit</button> </a>
                        </td>
                        <td>
                        <form action=".  $_SERVER['PHP_SELF']." method='POST' >
                        <input type='number' hidden value=".$room['room_id']." name='roomUid' /> 
                        <button type='submit' class='delete-button'> ";if($room['room_status'] =='active') echo "Delete";
                        else echo"Force Delete";
                        echo"
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
