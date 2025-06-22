<?php
session_start();
    // Get booking/payment details from session
    $pending = $_SESSION['pending_booking'] ?? null;
    if ($pending) {
        $amount = (int)($pending['room_price']) * 100; // Khalti expects paisa
        // Use updated session keys for user info
        $customer_name = $_SESSION['user_name'] ?? $pending['customer_name'];
        $customer_email = $_SESSION['user_email'] ?? $pending['customer_email'];
        $customer_number = $_SESSION['user_number'] ?? $pending['customer_number'];
        $room_id = $pending['room_id'];
        $order_id = 'ROOM_' . $room_id . '_' . time();
        $order_name = 'Room Booking #' . $room_id;
        $return_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/khalti_return.php';
        $website_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

        $payload = json_encode([
            'return_url' => $return_url,
            'website_url' => $website_url,
            'amount' => $amount,
            'purchase_order_id' => $order_id,
            'purchase_order_name' => $order_name,
            'customer_info' => [
                'name' => $customer_name,
                'email' => $customer_email,
                'phone' => $customer_number
            ]
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if (isset($result['payment_url'])) {
            header('Location: ' . $result['payment_url']);
            exit;
        } else {
            echo $response;
            exit;
        }
    } else {
        echo 'No pending booking found.';
        exit;
    }