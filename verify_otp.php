<?php
session_start();
require('admin/dbConnect.php');

// Get user_id from GET or POST
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : 0);
if (!$user_id) {
    die('Invalid request.');
}

// Fetch OTP record
$otpRow = null;
$res = $conn->query("SELECT * FROM otp_verifications WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    $otpRow = $res->fetch_assoc();
} else {
    die('No OTP request found.');
}

$error = $success = '';
$max_tries = 5;
$max_resend = 3;

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $input_otp = $_POST['otp_code'] ?? '';
    if ($otpRow['is_verified']) {
        $error = 'OTP already verified.';
    } elseif ($otpRow['tries'] >= $max_tries) {
        $error = 'Maximum verification attempts exceeded.';
    } elseif (strtotime($otpRow['expires_at']) < time()) {
        $error = 'OTP expired. Please resend.';
    } elseif ($input_otp == $otpRow['otp_code']) {
        // Mark OTP as verified
        $conn->query("UPDATE otp_verifications SET is_verified=1 WHERE id='{$otpRow['id']}'");
        // Activate user
        $conn->query("UPDATE users SET user_status='active' WHERE user_id='$user_id'");
        // Success message and redirect
        set_flash('success', 'Your account has been verified! You can now log in.');
        header('Location: login.php');
        exit();
    } else {
        // Increment tries
        $conn->query("UPDATE otp_verifications SET tries = tries + 1 WHERE id='{$otpRow['id']}'");
        $error = 'Invalid OTP. Please try again.';
        // Refresh OTP row
        $res = $conn->query("SELECT * FROM otp_verifications WHERE id='{$otpRow['id']}'");
        $otpRow = $res ? $res->fetch_assoc() : $otpRow;
    }
}

// Handle OTP resend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if ($otpRow['resend_count'] >= $max_resend) {
        $error = 'Resend limit reached.';
    } else {
        // Generate new OTP
        $new_otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $conn->query("UPDATE otp_verifications SET otp_code='$new_otp', expires_at='$expires_at', tries=0, resend_count=resend_count+1, is_verified=0 WHERE id='{$otpRow['id']}'");
        // TODO: Send OTP via email/SMS here
        $success = 'A new OTP has been sent.';
        // Refresh OTP row
        $res = $conn->query("SELECT * FROM otp_verifications WHERE id='{$otpRow['id']}'");
        $otpRow = $res ? $res->fetch_assoc() : $otpRow;
    }
}

function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}
function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Room Finder Nepal</title>
    <link rel="stylesheet" href="admin/login.css">
    <style>
        .otp-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: url('admin/uploads/67ae0a34aac00_backgroundLogin.png') center/cover no-repeat; }
        .otp-card { background: rgba(255,255,255,0.18); border-radius: 18px; box-shadow: 0 8px 32px #0002; padding: 2.5rem 2rem; max-width: 95vw; width: 370px; text-align: center; backdrop-filter: blur(8px); }
        .otp-input { font-size: 1.5rem; letter-spacing: 0.5rem; text-align: center; padding: 0.7rem 1rem; border-radius: 8px; border: 2px solid #ccc; width: 80%; margin-bottom: 1.2rem; background: #fff8; }
        .otp-btn { background: linear-gradient(135deg, var(--primary-color), var(--primary-hover)); color: white; border: none; padding: 0.7rem 1.5rem; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; margin: 0.5rem 0.5rem 0 0; transition: all 0.3s; }
        .otp-btn:disabled { background: #ccc; cursor: not-allowed; }
        .otp-info { color: #555; font-size: 0.98rem; margin-bottom: 1.2rem; }
        .otp-error { color: #e53935; margin-bottom: 1rem; }
        .otp-success { color: #388e3c; margin-bottom: 1rem; }
        @media (max-width: 500px) { .otp-card { padding: 1.2rem 0.5rem; width: 98vw; } }
    </style>
</head>
<body>
<div class="otp-wrapper">
    <div class="otp-card glass">
        <h2>OTP Verification</h2>
        <div class="otp-info">
            Please enter the 6-digit OTP sent to your email/phone.<br>
            <b>Attempts left:</b> <?php echo max(0, $max_tries - $otpRow['tries']); ?>,
            <b>Resends left:</b> <?php echo max(0, $max_resend - $otpRow['resend_count']); ?>
        </div>
        <?php $flash_error = get_flash('error'); if ($flash_error): ?>
            <div class="danger-notify" id="flash-danger"><span><?php echo $flash_error; ?></span></div>
        <?php endif; ?>
        <?php $flash_success = get_flash('success'); if ($flash_success): ?>
            <div class="success-notify" id="flash-success"><span><?php echo $flash_success; ?></span></div>
        <?php endif; ?>
        <form method="POST" style="margin-bottom:1.2rem;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <input class="otp-input" type="text" name="otp_code" maxlength="6" pattern="\d{6}" required placeholder="------" autocomplete="one-time-code">
            <div>
                <button class="otp-btn" type="submit" name="verify_otp" <?php echo ($otpRow['tries'] >= $max_tries || $otpRow['is_verified']) ? 'disabled' : ''; ?>>Verify OTP</button>
                <button class="otp-btn" type="submit" name="resend_otp" <?php echo ($otpRow['resend_count'] >= $max_resend || $otpRow['is_verified']) ? 'disabled' : ''; ?>>Resend OTP</button>
            </div>
        </form>
        <div class="otp-info">Didn't receive the code? Check your spam or try resending.</div>
    </div>
</div>
<script>
    // Ensure flash popups are visible for 5 seconds
    window.addEventListener('DOMContentLoaded', function() {
        const danger = document.getElementById('flash-danger');
        const success = document.getElementById('flash-success');
        [danger, success].forEach(function(el) {
            if (el) {
                el.style.display = 'block';
                setTimeout(function() {
                    el.style.opacity = '0';
                    setTimeout(function() { el.remove(); }, 1000);
                }, 5000);
            }
        });
    });
</script>
</body>
</html> 