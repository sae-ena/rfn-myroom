<?php
require_once "leftSidebar.php";
require "dbConnect.php";
require_once __DIR__ . '/../helperFunction/helpers.php';

// Payment & Booking History for Admin
$where = [];
if (!empty($_GET['payment_method'])) {
    $method = $conn->real_escape_string($_GET['payment_method']);
    $where[] = "payment_method = '$method'";
}
if (!empty($_GET['payment_status'])) {
    $status = $conn->real_escape_string($_GET['payment_status']);
    $where[] = "payment_status = '$status'";
}
// Text search
$search = $_GET['title'] ?? '';
$search = convertToNullIfEmpty($search);
if ($search) {
    $searchEsc = $conn->real_escape_string($search);
    $where[] = "(
        u.user_name LIKE '%$searchEsc%' OR
        u.user_email LIKE '%$searchEsc%' OR
        r.room_name LIKE '%$searchEsc%' OR
        b.payment_txn_id LIKE '%$searchEsc%'
    )";
}
$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
$query = "SELECT b.booking_id, b.user_id, u.user_name, u.user_email, u.user_number, b.room_id, r.room_name, r.room_location, b.booking_date, b.payment_method, b.payment_status, b.payment_txn_id, b.room_price FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN rooms r ON b.room_id = r.room_id $whereSql ORDER BY b.booking_date DESC LIMIT 100";
$result = $conn->query($query);

// Prepare data for the modular table template
$tableTitle = 'Payment & Booking History';
$addUrl = '';
$showBulkActions = false;
$searchQuery = $_GET['title'] ?? '';
$searchPlaceholder = 'Search by User, Email, Room, Txn/Ref ID';
$tableHeaders = ['User', 'Email', 'Phone', 'Room', 'Location', 'Price', 'Payment Method', 'Payment Status', 'Txn/Ref ID', 'Booking Date'];
$tableRows = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $tableRows[] = [
            'user_name' => htmlspecialchars($row['user_name'] ?? ''),
            'user_email' => htmlspecialchars($row['user_email'] ?? ''),
            'user_number' => htmlspecialchars($row['user_number'] ?? ''),
            'room_name' => htmlspecialchars($row['room_name'] ?? ''),
            'room_location' => htmlspecialchars($row['room_location'] ?? ''),
            'room_price' => 'Rs ' . number_format($row['room_price'] ?? 0),
            'payment_method' => ucfirst($row['payment_method'] ?? ''),
            'payment_status' => '<span style="color:' . ((($row['payment_status'] ?? '') === 'paid') ? 'green' : '#c82333') . ';font-weight:bold;">' . ucfirst($row['payment_status'] ?? '') . '</span>',
            'payment_txn_id' => htmlspecialchars($row['payment_txn_id'] ?? ''),
            'booking_date' => htmlspecialchars($row['booking_date'] ?? '')
        ];
    }
}

$customFilters = [
    // Payment Method dropdown
    '<select name="payment_method" id="payment_method" class="admin-table-status-filter" style="min-width:120px;">
        <option value="">All Methods</option>
        <option value="cash" ' . (isset($_GET['payment_method']) && $_GET['payment_method'] === 'cash' ? 'selected' : '') . '>Cash on Hand</option>
        <option value="khalti" ' . (isset($_GET['payment_method']) && $_GET['payment_method'] === 'khalti' ? 'selected' : '') . '>Khalti</option>
        <option value="esewa" ' . (isset($_GET['payment_method']) && $_GET['payment_method'] === 'esewa' ? 'selected' : '') . '>eSewa</option>
    </select>',
    // Payment Status dropdown
    '<select name="payment_status" id="payment_status" class="admin-table-status-filter" style="min-width:120px;">
        <option value="">All Status</option>
        <option value="paid" ' . (isset($_GET['payment_status']) && $_GET['payment_status'] === 'paid' ? 'selected' : '') . '>Paid</option>
        <option value="pending" ' . (isset($_GET['payment_status']) && $_GET['payment_status'] === 'pending' ? 'selected' : '') . '>Pending</option>
        <option value="failed" ' . (isset($_GET['payment_status']) && $_GET['payment_status'] === 'failed' ? 'selected' : '') . '>Failed</option>
    </select>'
];

// Get popup message from session and clear it
$popupMessage = $_SESSION['popup_message'] ?? '';
$popupType = $_SESSION['popup_type'] ?? '';
unset($_SESSION['popup_message'], $_SESSION['popup_type']);
?>
<div class="dashboard-content">
  
    <div class="container" style="display: flex; flex-direction: column; align-items: center;">
        <?php include 'tableTemplate.php'; ?>
    </div>
</div>
