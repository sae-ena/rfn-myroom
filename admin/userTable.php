<?php
require "leftSidebar.php";
require "dbConnect.php";  

$query = "SELECT * from users where user_type != 'admin' ;";
$result = $conn->query($query);

if(!$result) exit("Connection failed to fetch Data");

//Delete Operation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
   $roomId = $_POST['roomUid'];
    $query = "UPDATE users SET user_status = 'inActive' WHERE user_id = $roomId;";
   $deleteResult = $conn->query($query);

   if(!$deleteResult) exit("Connection failed to fetch Data");

   $successfullyDeleted = "User has been deleted";

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
   $userId = $_POST['statusChange'];
   $userCurrentStatus = $_POST['statusValue'];
   if($userCurrentStatus == 'active') $userCurrentStatus = 'inActive';
   else $userCurrentStatus = 'active';
    $query = "UPDATE users SET user_status = '$userCurrentStatus' WHERE user_id = $userId;";
   $deleteResult = $conn->query($query);

   if(!$deleteResult) exit("Connection failed to fetch Data");

   $successfullyDeleted = "UserID : ".$userId." status updated to ".$userCurrentStatus;

}



 ?>  
 
 <?php if (isset($successfullyDeleted)): ?>
            <div class="success-notify">
                <span><?php echo $successfullyDeleted; ?></span>
            </div>
        <?php endif; ?>
    <div class="dashboard-content">
        <h1 class="roomH1" style="color:white">Users Table</h1>
        <table class="room-table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Number</th>
                    <th>Status</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
           <?php if($result->num_rows >0){
            while($room = $result->fetch_assoc()){
               echo"<tr>
                    <td>".$room['user_id']."</td>
                    <td>".$room['user_name']."</td>
                    <td>".$room['user_email']."</td>
                    <td>".$room['user_number']."</td>
                    <td> 
                     <form action=".  $_SERVER['PHP_SELF']." method='POST' >
                     <input type='text' hidden value=".$room['user_id']." name='statusChange' />
                     <input type='text' hidden value=".$room['user_status']." name='statusValue' />
                    ";
                    if($room['user_status'] =='active') echo "<button class='edit-button' style=''>Active</button>";
                    else echo "<button class='delete-button'>InActive</button>";
                    echo
                    "
                       </form>
                    </td>
                    <td>".$room['user_location']."</td>
                    
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
            window.location.href = 'userTable.php'; 
        },300);
        <?php endif; ?>
        </script>
</html>
