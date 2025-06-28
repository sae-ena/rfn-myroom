<?php
require_once "leftSidebar.php";
require "dbConnect.php";  

$query = "SELECT * from form_managers ;";
$result = $conn->query($query);

if(!$result) exit("Connection failed to fetch Data");

//Delete Operation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formId'])) {
   $roomId = $_POST['formId'];
    $query = "UPDATE form_managers SET status = 0 WHERE form_id = $roomId;";
   $deleteResult = $conn->query($query);

   if(!$deleteResult) exit("Connection failed to fetch Data");

   $successfullyDeleted = "User has been deleted";

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
   $userId = $_POST['statusChange'];
   $userCurrentStatus = $_POST['statusValue'];
   if($userCurrentStatus == 1) $userCurrentStatus = 0;
   else $userCurrentStatus = 1;
    $query = "UPDATE form_managers SET status = '$userCurrentStatus' WHERE form_id = '$userId';";
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
        <h1 class="roomH1" style="color:white">DynaForm Manager</h1>
        <div style="text-align: right; width: 100%; margin-bottom: 20px;">
            <button>
                <a href="../dynaform/dynaform.php" class="add-room-button" style="background-color:rgb(4, 202, 14); color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                   Add New Form
                </a>
            </button>
        </div>
        <table class="room-table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Updated Date</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
           <?php if($result->num_rows >0){
               
               $sn = 1;
               while($room = $result->fetch_assoc()){
                $formattedDate = date("Y-m-d", strtotime($room['updated_at']));
               echo"<tr>
                    <td>".$sn++."</td>
                    <td>".$room['form_name']."</td>
                    <td> 
                     <form action=".  $_SERVER['PHP_SELF']." method='POST' >
                     <input type='text' hidden value=".$room['form_id']." name='statusChange' />
                     <input type='text' hidden value=".$room['status']." name='statusValue' />
                    ";
                    if($room['status'] == 1) echo "<button class='edit-button' style=''>Active</button>";
                    else echo "<button class='delete-button'>InActive</button>";
                    echo
                    "
                       </form>
                    </td>
                    <td>".$formattedDate."</td>
                    <td>
                        <a href='../dynaform/dynaform.php?formId=".$room['form_id']."'><button class='edit-button' style='left: 16px; top: 8px;background-color:rgb(9, 184, 253);'>Edit</button> </a>
                        <a href='../dynaform/form.php?formid=".$room['form_id']."'><button class='edit-button' style='left: 16px; top: 8px;'>View Form</button> </a>
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
            window.location.href = 'formManagerTable.php'; 
        },300);
        <?php endif; ?>
        </script>
</html>
