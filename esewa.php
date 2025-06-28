<?php
// eSewa UAT HMAC SHA-256 signature generation and cURL POST example
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . '/helperFunction/RoomFetchForWebsite.php');
require_once(__DIR__ . '/helperFunction/InsertRoomData.php');

// Get booking/payment details from session
$pending = $_SESSION['pending_booking'] ?? null;
if ($pending) {
    $amount = (int)($pending['room_price']);
    $payment_method = $pending['payment_method'] ;
         // Use updated session keys for user info
      $auth_id = $_SESSION['auth_id'];
         $time = date("Y-m-d H:i:s");
         $remarks = $pending['remarks'] ?? '';
    // Use updated session keys for user info
    $customer_name = $_SESSION['user_name'] ?? $pending['customer_name'];
    $customer_email = $_SESSION['user_email'] ?? $pending['customer_email'];
    $customer_number = $_SESSION['user_number'] ?? $pending['customer_number'];
    $room_id = $pending['room_id'];
    $order_id = 'ROOM_' . $room_id . '_' . time();
    $product_code = 'EPAYTEST'; // Use your actual product code
    $transaction_uuid = uniqid();
    $total_amount = $amount;
    $tax_amount = 0;
    $delivery_charge = 0;
    $service_charge = 0;
    $success_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/esewa_return.php';
    $failure_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/esewa_return.php';
    $signed_field_names = 'total_amount='.$total_amount.',transaction_uuid='.$transaction_uuid.',product_code='.$product_code;
    $message ='total_amount,transaction_uuid,product_code';
    $secret = '8gBm/:&EnhH.1/q'; // UAT SecretKey
    $hash = hash_hmac('sha256', $signed_field_names, $secret, true);
    $signature = base64_encode($hash);
    $data = [
        'amount' => $amount,
        'tax_amount' => $tax_amount,
        'total_amount' => $total_amount,
        'transaction_uuid' => $transaction_uuid,
        'product_code' => $product_code,
        'product_delivery_charge' => $delivery_charge,
        'product_service_charge' => $service_charge,
        'success_url' => $success_url,
        'failure_url' => $failure_url,
        'signed_field_names' => $message,
        'signature' => $signature
    ];
    // Redirect to eSewa form with POST (auto-submit)
    echo '<form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">';
    foreach ($data as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '</form>';
    
    echo '<script>document.getElementById("esewaForm").submit();</script>';
    $query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 1 ;";
    $bookingResult = RoomFetchForWebsite::fetchBookingData($query);
    if ($bookingResult == "No Booking Found") {
        $check_query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 0 ";
        $existingBooking = RoomFetchForWebsite::fetchExistingData($check_query);
        if (is_array($existingBooking)) {
            if(isset($remarks) && is_string($remarks)){
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 0, description = '$remarks', payment_method = '$payment_method', room_price = '$amount' , payment_txn_id = $transaction_uuid WHERE user_id = '$auth_id' AND room_id = '$room_id'";
            }else{
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 0, payment_method = '$payment_method', room_price = '$amount',payment_txn_id = $transaction_uuid  WHERE user_id = '$auth_id' AND room_id = '$room_id'";
            }
        } else {
            $query = "INSERT INTO bookings (user_id, room_id, description, booking_date, payment_method, room_price , payment_txn_id ,is_active) VALUES ('$auth_id', '$room_id','$remarks', '$time', '$payment_method', '$amount','$transaction_uuid ',0)";
        }
        $bookingResult1 = InsertRoomData::insertData($query);
   
    }
    echo 'Booking already exists. Please check your bookings.';
    exit;
} else {
    echo 'No pending booking found.';
    exit;
}
