<?php
session_start();
require('admin/dbConnect.php');
require_once('helperFunction/mail.php');

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

// Flash message helpers
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

$flash_error = get_flash('error');
$flash_success = get_flash('success');

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $input_otp = $_POST['otp_code'] ?? '';
    if ($otpRow['status'] == 'verified') {
        set_flash('error', 'OTP already verified.');
    } elseif ($otpRow['max_tries'] >= $max_tries) {
        set_flash('error', 'Maximum verification attempts exceeded.');
    } elseif (strtotime($otpRow['expires_at']) < time()) {
        set_flash('error', 'OTP expired. Please resend.');
    } elseif ($input_otp == $otpRow['otp']) {
        // Mark OTP as verified
        $conn->query("UPDATE otp_verifications SET `status`='verified' WHERE id='{$otpRow['id']}'");
        // Activate user
        $conn->query("UPDATE users SET user_status='active' WHERE user_id='$user_id'");
        set_flash('success', 'Your account has been verified! You can now log in.');
        header('Location: login.php');
        exit();
    } else {
        // Increment tries
        $conn->query("UPDATE otp_verifications SET max_tries = max_tries + 1 WHERE id='{$otpRow['id']}'");
        set_flash('error', 'Invalid OTP. Please try again.');
        // Refresh OTP row
        $res = $conn->query("SELECT * FROM otp_verifications WHERE id='{$otpRow['id']}'");
        $otpRow = $res ? $res->fetch_assoc() : $otpRow;
    }
    header("Location: verify_otp.php?user_id=$user_id");
    exit();
}

// Handle OTP resend (separate form, no OTP input required)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if ($otpRow['max_tries'] >= $max_resend) {
        set_flash('error', 'Resend limit reached.');
    } else {
        // Generate new OTP
        $new_otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));
        $conn->query("UPDATE otp_verifications SET otp='$new_otp', expires_at='$expires_at', max_tries= max_tries+1, status='pending' WHERE id='{$otpRow['id']}'");
        // Send OTP via email (using mail helper)
        $userRes = $conn->query("SELECT user_name, user_email FROM users WHERE user_id='$user_id' LIMIT 1");
        $userRow = $userRes ? $userRes->fetch_assoc() : null;
        if ($userRow) {
            list($subject, $message) = getOtpEmailForUser($conn, $userRow['user_name'], $new_otp, 3);
            if (sendMailPHPMailer($userRow['user_email'], $subject, $message)) {
                set_flash('success', 'A new OTP has been sent to your email.');
            } else {
                set_flash('error', 'Failed to send OTP email. Please try again later.');
            }
        } else {
            set_flash('error', 'User not found for OTP resend.');
        }
        // Refresh OTP row
        $res = $conn->query("SELECT * FROM otp_verifications WHERE id='{$otpRow['id']}'");
        $otpRow = $res ? $res->fetch_assoc() : $otpRow;
    }
    header("Location: verify_otp.php?user_id=$user_id");
    exit();
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
        .danger-notify, .success-notify {
            position: fixed !important;
            top: 1.5rem !important;
            right: 1.5rem !important;
            left: auto !important;
            max-width: 350px;
            width: calc(100vw - 3rem);
            text-align: left;
            z-index: 2000;
            animation: fadeInRight 0.3s ease;
            transition: opacity 0.5s;
            transform: none !important;
        }
        @keyframes fadeInRight {
            from { opacity: 0; right: 0; }
            to { opacity: 1; right: 1.5rem; }
        }
        @media (max-width: 600px) {
            .danger-notify, .success-notify {
                right: 0.5rem !important;
                left: 0.5rem !important;
                max-width: none;
                width: auto;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
<div class="otp-wrapper">
    <div class="otp-card glass">
        <h2>OTP Verification</h2>
        <div class="otp-info">
            Please enter the 6-digit OTP sent to your email/phone.<br>
        </div>
        <form method="POST" style="margin-bottom:1.2rem;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <input class="otp-input" type="text" name="otp_code" maxlength="6" pattern="\d{6}" required placeholder="------" autocomplete="one-time-code">
            <div>
                <button class="otp-btn" type="submit" name="verify_otp" <?php echo (($otpRow['max_tries'] >= $max_tries) || ($otpRow['status'] == "verified")) ? 'disabled' : ''; ?>>Verify OTP</button>
            </div>
        </form>
        <form method="POST" style="margin-bottom:1.2rem;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <button class="otp-btn" type="submit" name="resend_otp" <?php echo (($otpRow['max_tries'] >= $max_tries) || ($otpRow['status'] == "verified")) ? 'disabled' : ''; ?>>Resend OTP</button>
        </form>
        <div class="otp-info">Didn't receive the code? Check your spam or try resending.</div>
    </div>
</div>
<!-- Single Popup Notification Container -->
<div id="popup-notify" class="danger-notify" style="display:none;"><span id="popup-message"></span></div>
<script>
    // Ensure flash popups are visible for 5 seconds
    window.addEventListener('DOMContentLoaded', function() {
        // Show popup notification for errors/success
        var popup = document.getElementById('popup-notify');
        var popupMsg = document.getElementById('popup-message');
        var msg = '';
        var type = 'danger';
        <?php
        $popup_message = '';
        $popup_type = 'danger';
        if ($flash_error) {
            $popup_message = $flash_error;
            $popup_type = 'danger';
        } elseif ($flash_success) {
            $popup_message = $flash_success;
            $popup_type = 'success';
        }
        ?>
        msg = <?php echo json_encode($popup_message); ?>;
        type = <?php echo json_encode($popup_type); ?>;
        if (msg && popup && popupMsg) {
            popupMsg.textContent = msg;
            popup.className = type === 'success' ? 'success-notify' : 'danger-notify';
            popup.style.display = 'block';
            popup.style.opacity = '1';
            setTimeout(function() {
                popup.style.opacity = '0';
                setTimeout(function() { popup.style.display = 'none'; }, 1000);
            }, 6000);
        }
    });
</script>
</body>
</html> 