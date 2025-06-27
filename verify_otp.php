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
$res = $conn->query("SELECT * FROM otp_verifications WHERE user_id = '$user_id' And status != 'verified' ORDER BY id DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    $otpRow = $res->fetch_assoc();
} else {
    header("Location:/");
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
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
        }
        .otp-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('admin/uploads/67ae0a34aac00_backgroundLogin.png') center/cover no-repeat;
        }
        .otp-card {
            background: rgba(255,255,255,0.25);
            border-radius: 24px;
            box-shadow: 0 8px 32px #0002, 0 1.5px 6px #0001;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 95vw;
            width: 380px;
            text-align: center;
            backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255,255,255,0.25);
            position: relative;
            overflow: hidden;
        }
        .otp-card::before {
            content: '';
            position: absolute;
            top: -40px; left: -40px;
            width: 120px; height: 120px;
            background: linear-gradient(135deg, #1976d2 0%, #ff9800 100%);
            opacity: 0.13;
            border-radius: 50%;
            z-index: 0;
        }
        .otp-card h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 0.7rem;
            letter-spacing: 0.5px;
            z-index: 1;
            position: relative;
        }
        .otp-info {
            color: #222;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
            background: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            z-index: 1;
            position: relative;
        }
        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.2rem;
        }
        .otp-digit {
            width: 44px;
            height: 54px;
            font-size: 2rem;
            text-align: center;
            border-radius: 10px;
            border: 2px solid #d1d5db;
            background: rgba(255,255,255,0.85);
            box-shadow: 0 1px 4px #0001;
            transition: border 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .otp-digit:focus {
            border: 2px solid #1976d2;
            box-shadow: 0 0 0 2px #1976d233;
            background: #fff;
        }
        .otp-btn {
            background: linear-gradient(90deg, #43a047 60%, #66bb6a 100%);
            color: white;
            border: none;
            padding: 0.8rem 2.2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin: 0.5rem 0.5rem 0 0;
            box-shadow: 0 2px 8px #43a04733;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            position: relative;
            overflow: hidden;
        }
        .otp-btn.resend {
            background: linear-gradient(90deg, #ff9800 60%, #ffb347 100%);
            color: #fff;
        }
        .otp-btn:active {
            transform: scale(0.97);
        }
        .otp-btn:disabled {
            background: #ccc;
            color: #888;
            cursor: not-allowed;
        }
        .otp-timer {
            font-size: 1.05rem;
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 1.1rem;
            letter-spacing: 1px;
        }
        .otp-error {
            color: #e53935;
            margin-bottom: 1rem;
            font-weight: 600;
            background: #fff0f0;
            border-radius: 6px;
            padding: 8px 10px;
        }
        .otp-success {
            color: #388e3c;
            margin-bottom: 1rem;
            font-weight: 600;
            background: #e8f5e9;
            border-radius: 6px;
            padding: 8px 10px;
        }
        @media (max-width: 500px) { .otp-card { padding: 1.2rem 0.5rem; width: 98vw; } .otp-inputs { gap: 5px; } .otp-digit { width: 36px; height: 44px; font-size: 1.3rem; } }
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
            Please enter the 6-digit OTP sent to your email/phone.
        </div>
        <div id="otp-timer" class="otp-timer"></div>
        <form id="otp-form" method="POST" style="margin-bottom:1.2rem;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <div class="otp-inputs">
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" pattern="\d" required>
            </div>
            <input type="hidden" id="otp_code" name="otp_code" maxlength="6" pattern="\d{6}" required autocomplete="one-time-code">
            <div>
                <button class="otp-btn" type="submit" name="verify_otp" <?php echo (($otpRow['max_tries'] >= $max_tries) || ($otpRow['status'] == "verified")) ? 'disabled' : ''; ?>>Verify OTP</button>
            </div>
        </form>
        <form method="POST" style="margin-bottom:1.2rem;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <button class="otp-btn resend" type="submit" name="resend_otp" <?php echo (($otpRow['max_tries'] >= $max_tries) || ($otpRow['status'] == "verified")) ? 'disabled' : ''; ?>>Resend OTP</button>
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

    // OTP input auto-advance and collect value
    const otpInputs = document.querySelectorAll('.otp-digit');
    const otpHidden = document.getElementById('otp_code');
    if (otpInputs.length && otpHidden) {
        otpInputs.forEach((input, idx) => {
            input.addEventListener('input', function(e) {
                if (this.value.length > 1) this.value = this.value.slice(0, 1);
                if (this.value && idx < otpInputs.length - 1) {
                    otpInputs[idx + 1].focus();
                }
                updateOtpHidden();
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) {
                    otpInputs[idx - 1].focus();
                }
            });
        });
        function updateOtpHidden() {
            otpHidden.value = Array.from(otpInputs).map(i => i.value).join('');
        }
    }

    // Timer for OTP expiry (3 minutes from last update)
    <?php if (!empty($otpRow['expires_at'])): ?>
    (function() {
        var expiresAt = new Date(<?php echo json_encode($otpRow['expires_at']); ?>.replace(' ', 'T'));
        var timerDiv = document.getElementById('otp-timer');
        function updateTimer() {
            var now = new Date();
            var diff = Math.max(0, Math.floor((expiresAt - now) / 1000));
            var min = Math.floor(diff / 60);
            var sec = diff % 60;
            timerDiv.textContent = diff > 0 ? `OTP expires in ${min}:${sec.toString().padStart(2, '0')}` : 'OTP expired. Please resend.';
            if (diff > 0) setTimeout(updateTimer, 1000);
        }
        updateTimer();
    })();
    <?php endif; ?>
</script>
</body>
</html> 