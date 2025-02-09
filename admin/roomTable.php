<?php
require "leftSidebar.php";
require "dbConnect.php";  
require('../helperFunction/InsertRoomData.php');
require('../helperFunction/RoomFetchForWebsite.php');
require('../helperFunction/helpers.php');

$query = "SELECT * from rooms where room_status = 'active' ORDER BY created_at DESC";
$result =    RoomFetchForWebsite::fetchRoomData($query);


//Delete Operation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
   $roomId = $_POST['roomUid'];
  
   $query = "DELETE from rooms where room_id= '$roomId' AND room_status = 'inActive';";
   $deleteResult = $conn->query($query);
   if($conn->affected_rows > 0){
       
       $form_error = "Room ID: ".$roomId." has been deleted";
    }else{
        $query = "UPDATE rooms SET room_status = 'inActive' WHERE room_id = '$roomId';";
        $sqlResult = InsertRoomData::insertData($query);
        $successfullyRoomAdded = "RoomID : ".$roomId." status changed";
        

   }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
    $userId = $_POST['statusChange'];
    $userCurrentStatus = $_POST['statusValue'];
    if($userCurrentStatus == 'active'){
         $userCurrentStatus = 'inActive';
         $query = "UPDATE rooms SET room_status = '$userCurrentStatus' WHERE room_id = '$userId';";
         $sqlResult = InsertRoomData::insertData($query);
 
        $successfullyRoomAdded = "Room ID : ".$userId." status updated to ".$userCurrentStatus;

}
    else{ 
        $checkingDelete = "SELECT * from rooms as r LEFT JOIN bookings as b ON r.room_id = b.room_id  where b.status ='confirmed' AND b.room_id = '$userId';";
        $alreadyConfirmed = RoomFetchForWebsite::fetchRoomData($checkingDelete);    
        if(is_array($alreadyConfirmed))  $form_error = "Status Can't be Changed ";
        if(! isset($form_error)){ 
         $userCurrentStatus = 'active';
         
         $query = "UPDATE rooms SET room_status = '$userCurrentStatus' WHERE room_id = '$userId';";
         $sqlResult = InsertRoomData::insertData($query);
         $successfullyRoomAdded = "Room ID : ".$userId." status updated to ".$userCurrentStatus;
        }
    }
     
 
 }
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['status']) )) {
    
   $status = $_GET['status']?? null;
   $status = convertToNullIfEmpty($status);
   $roomId = $_GET['search']?? null;
   $roomId = convertToNullIfEmpty($roomId);
 
   if(! isset($status) && isset($roomId))  $query = "SELECT * from rooms where room_id = '$roomId';";
   if(isset($status) && isset($roomId)){
       $query = "SELECT * from rooms where room_status = '$status' AND room_id = '$roomId';";
    }elseif(isset($status) || isset($roomId)){
        $query = "SELECT * from rooms where room_status = '$status' OR room_id = '$roomId';";
     }
    $result = $conn->query($query);

   if(!$result) exit("Connection failed to fetch Data");

}



 ?>  
 
        <div class="dashboard-content"> 
        <h1 class="roomH1" style="color:white">All Room Records</h1>
     <!-- Filter Section -->
     <div class="filter-section">
        <form method="GET" action="">
            <div class="form-row">
                <!-- Status Filter -->
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="" >------</option>
                    <option value="active" <?php echo isset($status) && $status == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inActive" <?php echo isset($status) && $status == 'inActive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <!-- Search Filter -->
                <label for="search">Search:</label>
                <input type="number" name="search" id="search" value="<?php echo isset($roomId)?  $roomId:"" ?>" placeholder="Room UID...">

                <!-- Filter Button -->
               
                <button type="reset" style="background-color:white;color:black;margin: x 20px;">Reset</button>
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
           <?php if(true){
            foreach($result as $room){
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
                
   
require('../helperFunction/SweetAlert.php');
}?>


              
                <!-- More rows as needed -->
            </tbody>
        </table>
    </div>
    </div>
 
    </div>
    

</body>
<script>

    <?php if (isset($successfullyDeleted) || isset($form_error) || isset($successfullyRoomAdded) ): ?>
        setTimeout(function() {
            window.location.href = 'roomTable.php'; 
        },1100);
        <?php endif; ?>
        </script> 
</html>

