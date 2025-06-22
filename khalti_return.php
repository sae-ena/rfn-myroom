<?php
// khalti_return.php: Handles Khalti payment return, verifies payment, and finalizes booking
session_start();
if (!isset($_SESSION['pending_booking'])) {
    renderKhaltiUI(false, 'No pending booking found.');
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
$payment_method = 'khalti';
$time = date('Y-m-d H:i:s');

// Khalti returns pidx in GET params for verification
$pidx = $_GET['pidx'] ?? null;
if (!$pidx) {
    renderKhaltiUI(false, 'Payment failed or cancelled.');
    exit;
}
// Verify payment with Khalti
$verify_url = 'https://dev.khalti.com/api/v2/epayment/lookup/';
$payload = json_encode(['pidx' => $pidx]);
$ch = curl_init($verify_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
    'Content-Type: application/json',
]);
$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response, true);
if (isset($result['status']) && $result['status'] === 'Completed') {
    // Payment successful, insert booking with payment details
    require_once('helperFunction/InsertRoomData.php');
    $txn_id = $result['transaction_id'] ?? $pidx;
    $query = "INSERT INTO bookings (user_id, room_id, description, booking_date, payment_method, customer_name, customer_email, customer_number, room_price, payment_status, payment_txn_id) VALUES ('$user_id', '$room_id', '$remarks', '$time', '$payment_method', '$customer_name', '$customer_email', '$customer_number', '$room_price', 'paid', '$txn_id')";
    $res = InsertRoomData::insertData($query);
    unset($_SESSION['pending_booking']);
    renderKhaltiUI(true, 'Payment Successful! Your booking is confirmed.');
} else {
    $errorMsg = 'Payment Verification Failed';
    if (isset($result['message'])) {
        $errorMsg .= ': ' . htmlspecialchars($result['message']);
    }
    renderKhaltiUI(false, $errorMsg);
}

function renderKhaltiUI($success, $message) {
    $khaltiColor = '#5C2D91';
    $icon = $success ? '<div class="khalti-anim-success"><svg width="80" height="80" viewBox="0 0 80 80"><circle cx="40" cy="40" r="38" stroke="#5C2D91" stroke-width="4" fill="#fff"/><polyline points="25,43 37,55 57,30" style="fill:none;stroke:#5C2D91;stroke-width:5;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:60;stroke-dashoffset:60;" class="khalti-check"/></svg></div>'
        : '<div class="khalti-anim-fail"><svg width="80" height="80" viewBox="0 0 80 80"><circle cx="40" cy="40" r="38" stroke="#E53935" stroke-width="4" fill="#fff"/><line x1="28" y1="28" x2="52" y2="52" style="stroke:#E53935;stroke-width:5;stroke-linecap:round;stroke-dasharray:34;stroke-dashoffset:34;" class="khalti-cross1"/><line x1="52" y1="28" x2="28" y2="52" style="stroke:#E53935;stroke-width:5;stroke-linecap:round;stroke-dasharray:34;stroke-dashoffset:34;" class="khalti-cross2"/></svg></div>';
    $btnText = $success ? 'Go to My Bookings' : 'Try Again';
    $btnHref = $success ? 'myBooking.php' : 'index.php';
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>Khalti Payment Status</title>
    <style>
    body { margin:0; background:#f7f7fa; font-family:'Segoe UI',Roboto,sans-serif; }
    .khalti-navbar { background: $khaltiColor; color: #fff; padding: 0.7rem 1.5rem; display: flex; align-items: center; box-shadow: 0 2px 8px #0001; }
    .khalti-navbar img { height: 32px; margin-right: 12px; }
    .khalti-navbar span { font-size: 1.3rem; font-weight: 600; letter-spacing: 1px; }
    .khalti-modal-bg { position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(44,19,80,0.18); display:flex; align-items:center; justify-content:center; z-index:1000; }
    .khalti-modal { background:#fff; border-radius:18px; box-shadow:0 8px 32px #5C2D9140; padding:2.5rem 2rem 2rem 2rem; max-width: 95vw; width: 370px; text-align:center; position:relative; animation: popin 0.5s cubic-bezier(.68,-0.55,.27,1.55); }
    @keyframes popin { 0%{transform:scale(0.7);opacity:0;} 100%{transform:scale(1);opacity:1;} }
    .khalti-anim-success, .khalti-anim-fail { margin-bottom: 1.2rem; }
    .khalti-check { animation: checkmark 0.7s 0.2s cubic-bezier(.68,-0.55,.27,1.55) forwards; }
    @keyframes checkmark { to { stroke-dashoffset: 0; } }
    .khalti-cross1, .khalti-cross2 { animation: crossmark 0.7s 0.2s cubic-bezier(.68,-0.55,.27,1.55) forwards; }
    @keyframes crossmark { to { stroke-dashoffset: 0; } }
    .khalti-modal h2 { color: $khaltiColor; font-size: 1.5rem; margin-bottom: 0.5rem; }
    .khalti-modal p { color: #333; font-size: 1.08rem; margin-bottom: 1.2rem; }
    .khalti-modal .khalti-btn { background: $khaltiColor; color: #fff; border: none; border-radius: 8px; padding: 0.7rem 1.5rem; font-size: 1.08rem; font-weight: 500; cursor:pointer; transition: background 0.2s; box-shadow:0 2px 8px #5C2D9120; }
    .khalti-modal .khalti-btn:hover { background: #47207a; }
    @media (max-width: 500px) { .khalti-modal { padding: 1.2rem 0.5rem 1.2rem 0.5rem; width: 98vw; } .khalti-navbar { padding: 0.7rem 0.7rem; } }
    </style></head><body>
    <div class='khalti-navbar'>
        <img src='/admin/uploads/khalti_white.png' alt='Khalti Logo'>
        <span>Khalti Payment</span>
    </div>
    <div class='khalti-modal-bg'>
        <div class='khalti-modal'>
            $icon
            <h2>" . ($success ? 'Payment Successful!' : 'Payment Failed') . "</h2>
            <p>" . htmlspecialchars($message) . "</p>
            <a href='$btnHref'><button class='khalti-btn'>$btnText</button></a>
        </div>
    </div>
    </body></html>";
}
?>
