<?php
// verifyKhaltiTxn.php: Verifies a Khalti transaction using the lookup API
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['pidx'])) {
    echo '<div style="padding:2rem;text-align:center;font-family:sans-serif;color:#c82333;">Invalid request. No pidx provided.</div>';
    exit;
}
$pidx = $_POST['pidx'];
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
$result = json_decode($response, true);
$status = isset($result['status']) ? $result['status'] : 'Unknown';
$amount = isset($result['total_amount']) ? $result['total_amount']/100 : null;
$ref = isset($result['transaction_id']) ? $result['transaction_id'] : '';
$created = isset($result['created_on']) ? $result['created_on'] : '';
$err = isset($result['message']) ? $result['message'] : '';
$color = $status === 'Completed' ? '#2ecc40' : ($status === 'Pending' ? '#f1c40f' : '#e74c3c');
echo "<div style='max-width:420px;margin:40px auto;padding:2.2rem 1.5rem 1.5rem 1.5rem;border-radius:16px;box-shadow:0 4px 32px #5C2D9140;background:#fff;font-family:Segoe UI,Roboto,sans-serif;text-align:center;'>";
echo "<img src='https://khalti.com/static/img/khalti-logo-purple.svg' alt='Khalti' style='height:38px;margin-bottom:18px;'>";
echo "<h2 style='color:$color;margin-bottom:0.7rem;'>Khalti Payment Status</h2>";
echo "<div style='font-size:1.1rem;margin-bottom:1.2rem;'><b>Status:</b> <span style='color:$color;font-weight:600;'>$status</span></div>";
if ($amount) echo "<div style='margin-bottom:0.7rem;'><b>Amount:</b> Rs $amount</div>";
if ($ref) echo "<div style='margin-bottom:0.7rem;'><b>Txn/Ref ID:</b> $ref</div>";
echo "<div style='margin-bottom:0.7rem;'><b>PIDX:</b> $pidx</div>";
if ($created) echo "<div style='margin-bottom:0.7rem;'><b>Created:</b> $created</div>";
if ($err) echo "<div style='color:#e74c3c;margin-top:1rem;'>$err</div>";
echo "<a href='paymentHistory.php' style='display:inline-block;margin-top:1.5rem;padding:8px 22px;background:#5C2D91;color:#fff;border-radius:6px;text-decoration:none;font-weight:500;'>Back to History</a>";
echo "</div>";
