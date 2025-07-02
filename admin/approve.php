<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Now include the sidebar and render the rest of the page
require_once "leftSidebar.php";
require "dbConnect.php";  
require_once __DIR__ . '/../helperFunction/FormTableHelper.php';
require_once __DIR__ . '/../helperFunction/helpers.php';

// Default query - complex JOIN for booked rooms
$query = "SELECT u.user_name, u.user_email, r.room_location, r.room_name, r.room_image, 
                 r.room_price, b.booking_date, b.status, b.booking_id
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN rooms r ON b.room_id = r.room_id
          WHERE r.room_status = 'inActive' AND b.status = 'confirmed'
          ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$rooms = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

// Search/filter logic
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['title'])) {
    $search = $_GET['title'] ?? null;
    $search = convertToNullIfEmpty($search);

    if (isset($search)) {
        // Search in room name, location, user name, or user email
        $searchQuery = "SELECT u.user_name, u.user_email, r.room_location, r.room_name, r.room_image, 
                               r.room_price, b.booking_date, b.status, b.booking_id
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        JOIN rooms r ON b.room_id = r.room_id
                        WHERE r.room_status = 'inActive' AND b.status = 'confirmed'
                        AND (r.room_name LIKE ? OR r.room_location LIKE ? OR u.user_name LIKE ? OR u.user_email LIKE ?)
                        ORDER BY b.booking_date DESC";
        
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rooms = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }
    }
} else {
    echo "<script>
    localStorage.removeItem('inputValue');
    localStorage.removeItem('cursorPosition');
</script>";
}

// Prepare data for the modular table template
$tableTitle = 'All Booked Room Records';
$addUrl = '';
$showBulkActions = false;
$searchQuery = $_GET['title'] ?? '';
$tableHeaders = ['S.N', 'Room Image', 'Room Title', 'Location', 'User Name', 'User Email', 'Booked Date', 'Action'];
$tableRows = [];
$key = 0;

if (is_array($rooms) && count($rooms) > 0) {
    foreach ($rooms as $room) {
        $row = [
            'id' => $room['booking_id'],
            'sn' => ++$key,
            'room_image' => '<img src="' . htmlspecialchars($room['room_image']) . '" alt="room image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">',
            'room_name' => htmlspecialchars($room['room_name']),
            'room_location' => htmlspecialchars($room['room_location']),
            'user_name' => htmlspecialchars($room['user_name']),
            'user_email' => htmlspecialchars($room['user_email']),
            'booking_date' => htmlspecialchars($room['booking_date']),
            'action' => '<a href="/admin/approveView.php?booking_id=' . $room['booking_id'] . '" class="action-btn view" title="View Details" style="padding: 6px 10px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" d="M1.5 12s3.5-7 10.5-7 10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12z"/>
                <circle cx="12" cy="12" r="3.5" stroke-width="2"/>
              </svg>
            </a>'
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

            if (storedValue) {
                input.value = storedValue; // Set the value of the input field
                if (cursorPos) {
                    input.setSelectionRange(cursorPos, cursorPos); // Set the cursor position
                } else {
                    input.setSelectionRange(input.value.length, input.value.length); // Set the cursor at the end if no position is stored
                }
            }

            input.placeholder = 'Search by Room Title, Location, User Name, User Email';
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

    <?php if (isset($_SESSION['popup_message'])): ?>
        showPopup('<?= addslashes($_SESSION['popup_message']) ?>', '<?= $_SESSION['popup_type'] ?>');
        <?php 
        unset($_SESSION['popup_message']);
        unset($_SESSION['popup_type']);
        ?>
    <?php endif; ?>
</script> 
</html>