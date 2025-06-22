<?php
// eSewa UAT HMAC SHA-256 signature generation and cURL POST example

session_start();

// Get booking/payment details from session
$pending = $_SESSION['pending_booking'] ?? null;
if ($pending) {
    $amount = (int)($pending['room_price']);
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
    $success_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/esewa_return.php?status=success';
    $failure_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/esewa_return.php?status=failure';
    $signed_field_names = 'total_amount='.$total_amount.',transaction_uuid='.$transaction_uuid.',product_code='.$product_code;
    $message = $total_amount . $transaction_uuid . $product_code;
    $secret = '8gBm/:&EnhH.1/q'; // UAT SecretKey
    $hash = hash_hmac('sha256', $message, $secret, true);
    $signature = base64_encode($hash);

    var_dump($signature); // Debugging: Check the generated signature
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
        'signed_field_names' => $signed_field_names,
        'signature' => $signature
    ];
    // Redirect to eSewa form with POST (auto-submit)
    echo '<form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">';
    foreach ($data as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '</form>';
    echo '<script>document.getElementById("esewaForm").submit();</script>';
    exit;
} else {
    echo 'No pending booking found.';
    exit;
}
