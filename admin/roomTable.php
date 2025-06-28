<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
file_put_contents(__DIR__ . '/debug.log', print_r($_POST, true), FILE_APPEND);
// Handle bulk actions (POST with bulk_action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['delete_ids'])) {
    require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
    $ids = array_map('intval', $_POST['delete_ids']);
    $action = $_POST['bulk_action'];
    $table = 'rooms';
    $column = 'room_id';
    $success = false;
    $msg = '';
    if ($action === 'activate') {
        $success = FormTableHelper::bulkActivate($table, $column, $ids, 'room_status', 'active');
        $msg = $success ? 'Selected rooms activated successfully.' : 'Failed to activate selected rooms.';
    } elseif ($action === 'inactivate') {
        $success = FormTableHelper::bulkInactivate($table, $column, $ids, 'room_status', 'inActive');
        $msg = $success ? 'Selected rooms inactivated successfully.' : 'Failed to inactivate selected rooms.';
    } elseif ($action === 'delete') {
        $success = FormTableHelper::bulkDelete($table, $column, $ids);
        $msg = $success ? 'Selected rooms deleted (soft delete) successfully.' : 'Failed to delete selected rooms.';
    }
    $_SESSION['popup_message'] = $msg;
    $_SESSION['popup_type'] = $success ? 'success' : 'danger';
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
// Handle single room delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
    require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
    $roomId = $_POST['roomUid'];
    global $conn;
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $query = "DELETE from rooms where room_id= '$roomId' AND room_status = 'inActive';";
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    $deleteResult = $conn->query($query);
    if($conn->affected_rows > 0){
        $_SESSION['popup_message'] = "Room ID: $roomId has been deleted";
        $_SESSION['popup_type'] = 'success';
    } else {
        $query = "UPDATE rooms SET room_status = 'inActive' WHERE room_id = '$roomId';";
        require_once __DIR__ . '/../helperFunction/InsertRoomData.php';
        $sqlResult = InsertRoomData::insertData($query);
        $_SESSION['popup_message'] = "RoomID : $roomId status changed";
        $_SESSION['popup_type'] = 'success';
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
// Handle single room status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
    require_once __DIR__ . '/../helperFunction/RoomFetchForWebsite.php';
    require_once __DIR__ . '/../helperFunction/InsertRoomData.php';
    $userId = $_POST['statusChange'];
    $userCurrentStatus = $_POST['statusValue'];
    global $conn;
    if($userCurrentStatus == 'active'){
        $userCurrentStatus = 'inActive';
        $query = "UPDATE rooms SET room_status = '$userCurrentStatus' WHERE room_id = '$userId';";
        $sqlResult = InsertRoomData::insertData($query);
        $_SESSION['popup_message'] = "Room ID : $userId status updated to $userCurrentStatus";
        $_SESSION['popup_type'] = 'success';
    } else {
        $checkingDelete = "SELECT * from rooms as r LEFT JOIN bookings as b ON r.room_id = b.room_id  where b.status ='confirmed' AND b.room_id = '$userId';";
        $alreadyConfirmed = RoomFetchForWebsite::fetchRoomData($checkingDelete);    
        if(is_array($alreadyConfirmed)) {
            $_SESSION['popup_message'] = "Status Can't be Changed ";
            $_SESSION['popup_type'] = 'danger';
        } else { 
            $userCurrentStatus = 'active';
            $query = "UPDATE rooms SET room_status = '$userCurrentStatus' WHERE room_id = '$userId';";
            $sqlResult = InsertRoomData::insertData($query);
            $_SESSION['popup_message'] = "Room ID : $userId status updated to $userCurrentStatus";
            $_SESSION['popup_type'] = 'success';
        }
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
// Now include the sidebar and render the rest of the page
require_once "leftSidebar.php";
require "dbConnect.php";  
require('../helperFunction/InsertRoomData.php');
require('../helperFunction/RoomFetchForWebsite.php');
require('../helperFunction/helpers.php');

// Default query
$query = "SELECT * from rooms where room_status = 'active' ORDER BY created_at DESC";
$result = RoomFetchForWebsite::fetchRoomData($query);

// Search/filter logic
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

// Prepare data for the modular table template
$tableTitle = 'All Room Records';
$addUrl = '/admin/form.php';
$searchQuery = $_GET['title'] ?? '';
$tableHeaders = ['S.N', 'Room Title', 'Location', 'Price', 'Room Type', 'Status', 'Action'];
$tableRows = [];
$key = 0;
if ($result) {
    foreach ($result as $room) {
        $isActive = $room['room_status'] == 'active';
        $row = [
            'id' => $room['room_id'],
            'sn' => ++$key,
            'room_name' => $room['room_name'],
            'room_location' => $room['room_location'],
            'room_price' => $room['room_price'] . '/month',
            'room_type' => $room['room_type'],
            'status' => '<form method="POST" style="display:inline;" onsubmit="event.stopPropagation();">
                <input type="hidden" name="statusChange" value="' . $room['room_id'] . '">
                <input type="hidden" name="statusValue" value="' . $room['room_status'] . '">
                <label class="switch ' . ($isActive ? 'switch-active' : 'switch-inactive') . '">
                  <input type="checkbox" name="toggleStatus" onchange="this.form.submit()" ' . ($isActive ? 'checked' : '') . '>
                  <span class="slider round"></span>
                </label>
              </form>',
            'action' => '<a href="/admin/form.php?id=' . $room['room_id'] . '" class="action-btn edit">Edit</a>'
        ];
        $tableRows[] = $row;
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

}else{
    echo "<script>
    localStorage.removeItem('inputValue');
    localStorage.removeItem('cursorPosition');
</script>";
}

?>
<div class="dashboard-content">
<?php
// Use the new modular table template
include 'tableTemplate.php';
?>
</div>

</body>
<script>
    function resetForm() {
        localStorage.removeItem('inputValue');
        localStorage.removeItem('cursorPosition');
    window.location.href = window.location.pathname; 
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

