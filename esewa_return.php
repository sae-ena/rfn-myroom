<?php
// esewa_return.php: Handles eSewa payment return, verifies payment, and finalizes booking
session_start();
if (!isset($_SESSION['pending_booking'])) {
   header('Location: /');
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
$auth_id = $_SESSION['auth_id'] ?? $user_id; // Fallback to user_id if auth_id is not set
$payment_method = 'esewa';
$time = date('Y-m-d H:i:s');

// Get the Base64 encoded data from eSewa
$encoded_data = $_GET['data'] ?? null;
if (!$encoded_data) {
    $message = 'Missing payment data from eSewa.';
     renderEsewaUI(false, $message, "", $room_price);
    exit;
}

// Decode the Base64 data
$decoded_data = base64_decode($encoded_data);
if ($decoded_data === false) {
    echo 'Invalid Base64 data received from eSewa.';
    exit;
}

// Parse the JSON response
$esewa_response = json_decode($decoded_data, true);
if ($esewa_response === null) {
    echo 'Invalid JSON data received from eSewa.';
    exit;
}

// Extract payment details from eSewa response
$transaction_code = $esewa_response['transaction_code'] ?? '';
$payment_status = $esewa_response['status'] ?? '';
$total_amount = $esewa_response['total_amount'] ?? 0;
$transaction_uuid = $esewa_response['transaction_uuid'] ?? '';
$product_code = $esewa_response['product_code'] ?? '';
$signature = $esewa_response['signature'] ?? '';

// Verify payment status
if ($payment_status !== 'COMPLETE') {
    echo 'Payment not completed. Status: ' . $payment_status;
     $check_query = "SELECT * FROM bookings WHERE user_id = '$auth_id' AND room_id = '$room_id' AND is_active = 0 ";
        $existingBooking = RoomFetchForWebsite::fetchExistingData($check_query);
        if (is_array($existingBooking)) {
            if(isset($remarks) && is_string($remarks)){
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, description = '$remarks', payment_method = '$payment_method', customer_name = '$customer_name', customer_email = '$customer_email', customer_number = '$customer_number', room_price = '$room_price' , payment_status = $paymentStatus WHERE user_id = '$auth_id' AND room_id = '$room_id' AND payment_txn_id = '$transaction_uuid'";
            }else{
                $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, payment_method = '$payment_method', customer_name = '$customer_name', customer_email = '$customer_email', customer_number = '$customer_number', room_price = '$room_price' ,payment_status = $paymentStatus WHERE user_id = '$auth_id' AND room_id = '$room_id' AND payment_txn_id = '$transaction_uuid'";
            }
        } else {
           
            
        }
        $bookingResult1 = InsertRoomData::insertData($query);
    exit;
}

// Verify amount matches (optional but recommended)
if ($total_amount != $room_price) {
    $message = 'Amount mismatch. Expected: ' . $room_price . ', Received: ' . $total_amount;
     renderEsewaUI(false, $message, $transaction_uuid, $total_amount);
    exit;
}

require_once('helperFunction/InsertRoomData.php');
 $query = "UPDATE bookings SET booking_date = '$time', is_active = 1, payment_status = 'paid' WHERE user_id = '$auth_id' AND room_id = '$room_id' AND payment_txn_id = '$transaction_uuid'";
$res = InsertRoomData::insertData($query);

if ($res) {
    unset($_SESSION['pending_booking']);
    renderEsewaUI(true, 'Payment Successful! Your booking is confirmed.', $transaction_uuid, $total_amount);
} else {
      unset($_SESSION['pending_booking']);
    renderEsewaUI(false, 'Payment Successful but Booking Failed! Please contact support with Transaction ID: ' . $transaction_uuid, $transaction_uuid, $total_amount);
}

function renderEsewaUI($success, $message, $txn_id = '', $amount = 0) {
    $esewaColor = '#60bb46';
    $icon = $success
        ? '<div class="esewa-anim-success"><svg width="80" height="80" viewBox="0 0 80 80"><circle cx="40" cy="40" r="38" stroke="#60bb46" stroke-width="4" fill="#fff"/><polyline points="25,43 37,55 57,30" style="fill:none;stroke:#60bb46;stroke-width:5;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:60;stroke-dashoffset:60;" class="esewa-check"/></svg></div>'
        : '<div class="esewa-anim-fail"><svg width="80" height="80" viewBox="0 0 80 80"><circle cx="40" cy="40" r="38" stroke="#E53935" stroke-width="4" fill="#fff"/><line x1="28" y1="28" x2="52" y2="52" style="stroke:#E53935;stroke-width:5;stroke-linecap:round;stroke-dasharray:34;stroke-dashoffset:34;" class="esewa-cross1"/><line x1="52" y1="28" x2="28" y2="52" style="stroke:#E53935;stroke-width:5;stroke-linecap:round;stroke-dasharray:34;stroke-dashoffset:34;" class="esewa-cross2"/></svg></div>';
    $btnText = $success ? 'Go to My Bookings' : 'Try Again';
    $btnHref = $success ? 'myBooking.php' : 'index.php';
    $amountText = $amount ? '<div class="esewa-amount">Amount: <b>Rs. ' . number_format($amount, 2) . '</b></div>' : '';
    $txnText = $txn_id ? '<div class="esewa-txn">Transaction ID: <b>' . htmlspecialchars($txn_id) . '</b></div>' : '';
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>eSewa Payment Status</title>
    <link rel='preconnect' href='https://fonts.googleapis.com'><link rel='preconnect' href='https://fonts.gstatic.com' crossorigin><link href='https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap' rel='stylesheet'>
    <style>
    body { margin:0; background:#f7f7fa; font-family:'Lato', 'Segoe UI', Arial, sans-serif; }
    .esewa-navbar { background: $esewaColor; color: #fff; padding: 0.7rem 1.5rem; display: flex; align-items: center; box-shadow: 0 2px 8px #0001; }
    .esewa-navbar img { height: 32px; margin-right: 12px; }
    .esewa-navbar span { font-size: 1.3rem; font-weight: 600; letter-spacing: 1px; }
    .esewa-modal-bg { position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(96,187,70,0.10); display:flex; align-items:center; justify-content:center; z-index:1000; }
    .esewa-modal { background:#fff; border-radius:18px; box-shadow:0 8px 32px #60bb4640; padding:2.5rem 2rem 2rem 2rem; max-width: 95vw; width: 370px; text-align:center; position:relative; animation: popin 0.5s cubic-bezier(.68,-0.55,.27,1.55); }
    @keyframes popin { 0%{transform:scale(0.7);opacity:0;} 100%{transform:scale(1);opacity:1;} }
    .esewa-anim-success, .esewa-anim-fail { margin-bottom: 1.2rem; }
    .esewa-check { animation: checkmark 0.7s 0.2s cubic-bezier(.68,-0.55,.27,1.55) forwards; }
    @keyframes checkmark { to { stroke-dashoffset: 0; } }
    .esewa-cross1, .esewa-cross2 { animation: crossmark 0.7s 0.2s cubic-bezier(.68,-0.55,.27,1.55) forwards; }
    @keyframes crossmark { to { stroke-dashoffset: 0; } }
    .esewa-modal h2 { color: $esewaColor; font-size: 1.5rem; margin-bottom: 0.5rem; }
    .esewa-modal p { color: #333; font-size: 1.08rem; margin-bottom: 1.2rem; }
    .esewa-modal .esewa-btn { background: $esewaColor; color: #fff; border: none; border-radius: 8px; padding: 0.7rem 1.5rem; font-size: 1.08rem; font-weight: 500; cursor:pointer; transition: background 0.2s; box-shadow:0 2px 8px #60bb4620; }
    .esewa-modal .esewa-btn:hover { background: #469a36; }
    .esewa-amount, .esewa-txn { color: #222; font-size: 1.08rem; margin-bottom: 0.5rem; }
    @media (max-width: 500px) { .esewa-modal { padding: 1.2rem 0.5rem 1.2rem 0.5rem; width: 98vw; } .esewa-navbar { padding: 0.7rem 0.7rem; } }
    </style></head><body>
    <div class='esewa-navbar'>
        <img src='/admin/uploads/esewa.jpg' alt='eSewa Logo' style='border-radius:6px;padding:2px 4px;height:32px;width: 69px;;'>
        <span>eSewa Payment</span>
    </div>
    <div class='esewa-modal-bg'>
        <div class='esewa-modal'>
            $icon
            <h2>" . ($success ? 'Payment Successful!' : 'Payment Failed') . "</h2>
            <p>" . htmlspecialchars($message) . "</p>
            $amountText
            $txnText
            <a href='$btnHref'><button class='esewa-btn'>$btnText</button></a>
        </div>
    </div>
    </body></html>";
}
