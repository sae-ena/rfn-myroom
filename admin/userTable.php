<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle bulk actions (POST with bulk_action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['delete_ids'])) {
    require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
    $ids = array_map('intval', $_POST['delete_ids']);
    $action = $_POST['bulk_action'];
    $table = 'users';
    $column = 'user_id';
    $success = false;
    $msg = '';
    
    if ($action === 'activate') {
        $success = FormTableHelper::bulkActivate($table, $column, $ids, 'user_status', 'active');
        $msg = $success ? 'Selected users activated successfully.' : 'Failed to activate selected users.';
    } elseif ($action === 'inactivate') {
        $success = FormTableHelper::bulkInactivate($table, $column, $ids, 'user_status', 'inActive');
        $msg = $success ? 'Selected users inactivated successfully.' : 'Failed to inactivate selected users.';
    } elseif ($action === 'delete') {
        $success = FormTableHelper::bulkInactivate($table, $column, $ids, 'user_status', 'inActive'); // Soft delete for users
        $msg = $success ? 'Selected users deleted (soft delete) successfully.' : 'Failed to delete selected users.';
    }
    
    $_SESSION['popup_message'] = $msg;
    $_SESSION['popup_type'] = $success ? 'success' : 'danger';
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle single user delete (preserve existing functionality)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roomUid'])) {
    require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
    $userId = $_POST['roomUid'];
    $success = FormTableHelper::bulkInactivate('users', 'user_id', [$userId], 'user_status', 'inActive');
    
    if($success){
        $_SESSION['popup_message'] = "User has been deleted";
        $_SESSION['popup_type'] = 'success';
    } else {
        $_SESSION['popup_message'] = "Failed to delete user";
        $_SESSION['popup_type'] = 'danger';
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle single user status change (preserve existing functionality)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
    require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
    $userId = $_POST['statusChange'];
    $userCurrentStatus = $_POST['statusValue'];
    
    if($userCurrentStatus == 'active'){
        $userCurrentStatus = 'inActive';
        $success = FormTableHelper::bulkInactivate('users', 'user_id', [$userId], 'user_status', 'inActive');
        $_SESSION['popup_message'] = "User ID : $userId status updated to $userCurrentStatus";
    } else {
        $userCurrentStatus = 'active';
        $success = FormTableHelper::bulkActivate('users', 'user_id', [$userId], 'user_status', 'active');
        $_SESSION['popup_message'] = "User ID : $userId status updated to $userCurrentStatus";
    }
    
    $_SESSION['popup_type'] = $success ? 'success' : 'danger';
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Now include the sidebar and render the rest of the page
require_once "leftSidebar.php";
require "dbConnect.php";  
require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
require_once __DIR__ . '/../helperFunction/helpers.php';

// Default query - get all non-admin users
$query = "SELECT * FROM users WHERE user_type != 'admin' ORDER BY created_at DESC";
$result = $conn->query($query);

// Search/filter logic
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['title']) || isset($_GET['status']))) {
    $search = $_GET['title'] ?? null;
    $search = convertToNullIfEmpty($search);
    $status = $_GET['status'] ?? null;
    $status = convertToNullIfEmpty($status);

    if (isset($status) && isset($search)) {
        // Search with status filter
        $searchResults = FormTableHelper::searchData(
            'users', 
            ['user_name', 'user_email', 'user_location'], 
            $search, 
            "user_type != 'admin' AND user_status = '$status'"
        );
        $result = $searchResults ? $searchResults : [];
    } elseif (isset($search)) {
        // Search only
        $searchResults = FormTableHelper::searchData(
            'users', 
            ['user_name', 'user_email', 'user_location'], 
            $search, 
            "user_type != 'admin'"
        );
        $result = $searchResults ? $searchResults : [];
    } elseif (isset($status)) {
        // Status filter only
        $query = "SELECT * FROM users WHERE user_type != 'admin' AND user_status = '$status' ORDER BY created_at DESC";
        $result = $conn->query($query);
        if (!$result) {
            $result = [];
        } else {
            $tempResult = [];
            while ($row = $result->fetch_assoc()) {
                $tempResult[] = $row;
            }
            $result = $tempResult;
        }
    }
} else {
    // No search/filter - get all results as array
    if ($result) {
        $tempResult = [];
        while ($row = $result->fetch_assoc()) {
            $tempResult[] = $row;
        }
        $result = $tempResult;
    } else {
        $result = [];
    }
}

// Prepare data for the modular table template
$tableTitle = 'All User Records';
$addUrl = '#'; // No add user functionality in admin
$searchQuery = $_GET['title'] ?? '';
$tableHeaders = ['S.N', 'Name', 'Email', 'Number', 'Status', 'Location'];
$tableRows = [];
$key = 0;

if ($result && is_array($result)) {
    foreach ($result as $user) {
        $isActive = $user['user_status'] == 'active';
        $row = [
            'id' => $user['user_id'],
            'sn' => ++$key,
            'user_name' => $user['user_name'],
            'user_email' => $user['user_email'],
            'user_number' => $user['user_number'],
            'Status' => '<form method="POST" style="display:inline;" onsubmit="event.stopPropagation();">
                <input type="hidden" name="statusChange" value="' . $user['user_id'] . '">
                <input type="hidden" name="statusValue" value="' . $user['user_status'] . '">
                <label class="switch ' . ($isActive ? 'switch-active' : 'switch-inactive') . '">
                  <input type="checkbox" name="toggleStatus" onchange="this.form.submit()" ' . ($isActive ? 'checked' : '') . '>
                  <span class="slider round"></span>
                </label>
              </form>',
            'user_location' => $user['user_location']
        ];
        $tableRows[] = $row;
    }
}

// Get popup message from session and clear it
$popupMessage = $_SESSION['popup_message'] ?? '';
$popupType = $_SESSION['popup_type'] ?? '';
unset($_SESSION['popup_message'], $_SESSION['popup_type']);

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
            input.placeholder = 'Search by Name, Email, Location';

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
        timeout = setTimeout(autoSubmit(event), 2000); // Set a new timeout to call autoSubmit after 2 seconds
    });

    document.getElementById('status').addEventListener('input', autoSubmit);

    <?php if ($popupMessage): ?>
        showPopup('<?= addslashes($popupMessage) ?>', '<?= $popupType ?>');
    <?php endif; ?>
</script> 
</html>
