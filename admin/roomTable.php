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
  

   $conn->query("SET FOREIGN_KEY_CHECKS = 0");
   $query = "DELETE from rooms where room_id= '$roomId' AND room_status = 'inActive';";
   $conn->query("SET FOREIGN_KEY_CHECKS = 1");
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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['title']) || isset($_GET['status']) )) {
    $search =  $_GET['title']?? null ;
    $search = convertToNullIfEmpty($search);
   $status = $_GET['status']?? null;
   $status = convertToNullIfEmpty($status);
 
   if(isset($status) && isset($search)){
        $query = "SELECT * from rooms where (room_status = '$status') AND (room_location like '%$search%' OR room_name like '%$search%');";
     }elseif(isset($search)){
         $query = "SELECT * from rooms where (room_name like '%$search%' OR room_id like '%$search%') OR room_location like '%$search%';";
        
        
     }elseif(isset($status)){
         $query = "SELECT * from rooms where room_status = '$status' ";
        
        }
    
    $result = $conn->query($query);

   if(!$result) exit("Connection failed to fetch Data");

}



 ?>  
 
        <div class="dashboard-content"> 
        <h1 class="roomH1" style="color:white">All Room Records</h1>
     <!-- Filter Section -->
     <div class="filter-section">
        <form method="GET" action="" id="filterForm">
            <div class="form-row">
                <!-- Search Filter -->
                <input type="text" name="title" id="titleSearch" value="<?php echo isset($search)?  $search:"" ?>" placeholder="Search by Title ,ID ,Location" style="padding: 10px 15px; border: 1px solid #ccc; border-radius: 15px; width: 400px; max-width: 400px; font-size: 16px; box-sizing: border-box; transition: border-color 0.3s ease, box-shadow 0.3s ease; outline: none;" onfocus="this.style.borderColor='#007BFF'; this.style.boxShadow='0 0 10px rgba(0, 123, 255, 0.2)';" onblur="this.style.borderColor='#ccc'; this.style.boxShadow='none';">

                <!-- Status Filter -->
                <label for="status" style="margin-top: 12px;">Status:</label>
                <select name="status" id="status" style="border-radius:21px">
                    <option value=""selected disabled >---Status----</option>
                    <option value="active" <?php echo isset($status) && $status == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inActive" <?php echo isset($status) && $status == 'inActive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

              
    
    <button type="reset" style="background-color: #f8f9fa; color: #495057; border: 2px solid #ced4da; border-radius: 5px; padding: 12px 20px; font-size: 17px; cursor: pointer; transition: background-color 0.3s ease, border-color 0.3s ease;border-radius:29px;text-decoration:none" onclick="resetForm()" >Reset </button>


              
            </div>
        </form>
    </div>
        <table class="room-table">
            <thead>
                <tr>
                    <th>S.N</th>
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
            foreach($result as $key => $room){
               echo"<tr>
                    <td>".++$key."</td>
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
                        else echo"Delete";
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
    function resetForm() {
        localStorage.removeItem('inputValue');
        localStorage.removeItem('cursorPosition');
    window.location.href = window.location.pathname; // Reloads page without query parameters
}
window.onload = function() {
    const input = document.getElementById('titleSearch');
    const storedValue = localStorage.getItem('inputValue');
    const cursorPos = localStorage.getItem('cursorPosition');

    // Focus the input field on page load
    if (input) {
        input.focus(); // Automatically focus the input field

        if (storedValue) {
            input.value = storedValue; // Set the value of the input field
            if (cursorPos) {
                input.setSelectionRange(cursorPos, cursorPos); // Set the cursor position
            } else {
                input.setSelectionRange(input.value.length, input.value.length); // Set the cursor at the end if no position is stored
            }
        }
    }

    // Save the input value and cursor position on input change
    input.addEventListener('input', function(event) {
        const currentValue = input.value;
        const currentCursorPos = input.selectionStart; // Get the current cursor position
        
        localStorage.setItem('inputValue', currentValue); // Store the value
        localStorage.setItem('cursorPosition', currentCursorPos); // Store the cursor position
    });

    // Handle form submit to clear stored values
    const form = document.getElementById('filterForm');
    if (form) {
        form.addEventListener('submit', function() {
            localStorage.removeItem('inputValue');
            localStorage.removeItem('cursorPosition');
        });
    }
};



    // Function to submit the form when a change occurs
    function autoSubmit(event) {
        event.preventDefault(); 
        document.getElementById('filterForm').submit();

    document.getElementById('titleSearch').focus(); 
    const input = document.getElementById('titleSearch');
    const cursorPosition = input.selectionStart;  // Save current cursor position

    input.focus();
    input.setSelectionRange(cursorPosition, cursorPosition); 
    }
    
    document.getElementById('titleSearch').addEventListener('input', function(event) {
   
    timeout = setTimeout(autoSubmit(event), 2000); // Set a new timeout to call autoSubmit after 1 second
});

document.getElementById('status').addEventListener('input', autoSubmit);

    <?php if (isset($successfullyDeleted) || isset($form_error) || isset($successfullyRoomAdded) ): ?>
        setTimeout(function() {
            window.location.href = 'roomTable.php'; 
        },1100);
        <?php endif; ?>
        </script> 
</html>

