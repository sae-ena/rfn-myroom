<?php
// esewa_return.php: Handles eSewa payment return, verifies payment, and finalizes booking
session_start();
if (!isset($_SESSION['pending_booking'])) {
    echo 'No pending booking found.';
    exit;
}
$pending = $_SESSION['pending_booking'];
$room_id = $pending['room_id'];
$user_id = $pending['user_id'];
$remarks = $pending['remarks'];
$customer_name = $pending['customer_name'];
$customer_email = $pending['customer_email'];
$customer_number = $pending['customer_number'];
$room_price = $pending['room_price'];
$payment_method = 'esewa';
$time = date('Y-m-d H:i:s');

$status = $_GET['status'] ?? null;
if ($status !== 'success') {
    echo 'Payment failed or cancelled.';
    exit;
}
// eSewa returns refId in GET params for verification (in production, use POST/IPN for security)
$refId = $_GET['refId'] ?? null;
if (!$refId) {
    echo 'Missing payment reference.';
    exit;
}
// Optionally: verify with eSewa API (skipped for demo)
require_once('helperFunction/InsertRoomData.php');
$query = "INSERT INTO bookings (user_id, room_id, description, booking_date, payment_method, customer_name, customer_email, customer_number, room_price, payment_status, payment_txn_id) VALUES ('$user_id', '$room_id', '$remarks', '$time', '$payment_method', '$customer_name', '$customer_email', '$customer_number', '$room_price', 'paid', '$refId')";
$res = InsertRoomData::insertData($query);
unset($_SESSION['pending_booking']);
echo '<h2>Payment Successful!</h2><p>Your booking is confirmed.</p>';
echo '<a href="myBooking.php">Go to My Bookings</a>';
