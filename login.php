<?php
// Start the session at the very top
session_start();

require('admin/dbConnect.php');
require('helperFunction/InsertRoomData.php');

// Initialize variables for login data
$user_emailByLogin = "";
$user_passwordByLogin = "";
$form_error = null;
$show_signup = false;

// If the user is already logged in, redirect to index page
if (isset($_SESSION['user_email'])) {
    header("Location:index.php");
    exit(); // Ensure no further code is executed after the redirection
}

// Fetch backend settings for dynamic registration
$settings = [];
$settingsResult = $conn->query("SELECT name, value FROM backend_settings WHERE name IN ('otp-verification', 'user-singup-default-status')");
if ($settingsResult && $settingsResult->num_rows > 0) {
    while ($row = $settingsResult->fetch_assoc()) {
        $settings[$row['name']] = $row['value'];
    }
}
$otp_verification_enabled = isset($settings['otp-verification']) && $settings['otp-verification'] === '1';
$user_signup_default_status = isset($settings['user-singup-default-status']) ? $settings['user-singup-default-status'] : 'inActive';

// Registration logic (re-added)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $user_name = $user_email = $user_number = $user_location = $user_password = "";
    // Get form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_number = $_POST['user_number'];
    $user_location = $_POST['user_location'];
    $user_password = $_POST['user_password'];
    $user_confirmation_password = $_POST['user_confirmation_password'];

    // Validate input
    if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_confirmation_password) || empty($user_number)) {
        $form_error = "Please fill all required fields.";
    } elseif ($user_password !== $user_confirmation_password) {
        $form_error = "Passwords do not match.";
    } else {
        // Check for unique email
        $uniqueCheck = "SELECT user_email FROM users WHERE user_email = '$user_email' ";
        $result = $conn->query($uniqueCheck);
        if ($result->num_rows > 0) {
            $form_error = "Email already exists";
        } else {
            // Check for unique phone number
            $uniqueCheck = "SELECT user_number FROM users WHERE user_number = '$user_number' ";
            $result = $conn->query($uniqueCheck);
            if ($result->num_rows > 0) {
                $form_error = "PhoneNumber already exists";
            } elseif (!str_contains($user_email, '@')) {
                $form_error = "Invalid Email Format";
            } elseif (strlen($user_number) != 10 || !str_starts_with($user_number, '98')) {
                $form_error = "Invalid PhoneNumber.";
            } elseif (strlen($user_password) < 8) {
                $form_error = "Password must be at least 8 characters long.";
            } elseif (!preg_match('/[A-Z]/', $user_password)) {
                $form_error = "Password must contain at least one uppercase letter.";
            } elseif (!preg_match('/[0-9]/', $user_password)) {
                $form_error = "Password must contain at least one number.";
            } elseif (!preg_match('/[^a-zA-Z0-9]/', $user_password)) {
                $form_error = "Password must contain at least one special character.";
            } else {
                // Hash the password
                $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
                // Prepare the SQL query to insert data into the 'users' table
                $query = "INSERT INTO users (user_name, user_email, user_number, user_location, user_status, user_password, user_type) VALUES ('$user_name', '$user_email', '$user_number', '$user_location', '" . $conn->real_escape_string($user_signup_default_status) . "', '$hashed_password', 'user')";
                $sqlResult = InsertRoomData::insertData($query);
                if ($sqlResult) {
                    // Get the inserted user_id
                    $user_id = $conn->insert_id;
                    if (!$user_id) {
                        // fallback: fetch by email
                        $res = $conn->query("SELECT user_id FROM users WHERE user_email = '$user_email' LIMIT 1");
                        $row = $res ? $res->fetch_assoc() : null;
                        $user_id = $row ? $row['user_id'] : null;
                    }
                    if ($otp_verification_enabled) {
                        // Generate OTP
                        $otp_code = rand(100000, 999999);
                        $expires_at = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                        // Insert OTP into otp_verifications
                        $conn->query("INSERT INTO otp_verifications (user_id, otp, expires_at, max_tries, `status`, created_at) VALUES ('$user_id', '$otp_code', '$expires_at', 0, 'pending', NOW())");
                        // Send OTP email (now using PHPMailer)
                        require_once('helperFunction/mail.php');
                        $expires_minutes = 2;
                        list($subject, $message) = getOtpEmailForUser($conn, $user_name, $otp_code, $expires_minutes);
                        if (!sendMailPHPMailer($user_email, $subject, $message)) {
                            // Optionally log error or set $form_error
                            // $form_error = "Failed to send OTP email. Please check your email address.";
                        }
                        // Redirect to OTP verification page
                        header("Location: verify_otp.php?user_id=$user_id");
                        exit();
                    } else {
                        // OTP not required, log in or activate user directly
                        // You can set session and redirect to dashboard or login page
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_email'] = $user_email;
                        $_SESSION['user_name'] = $user_name;
                        // Redirect to dashboard or login page
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    $form_error = "Registration failed. Please try again later.";
                }
            }
        }
    }
    if ($form_error) {
        $show_signup = true;
    }
}

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Get the input data from the form
    $user_emailByLogin = $_POST['userEmailByLogin'];
    $user_passwordByLogin = $_POST['userPasswordByLogin'];

    // Check if email and password are not empty
    if (!empty($user_emailByLogin) && !empty($user_passwordByLogin)) {
        // Query to check if the user email exists in the database
        $query = "SELECT user_id, user_password, user_type, user_status, user_name, user_number FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
        $result = $conn->query($query);

        // Check if the query returned a result (i.e., the email exists)
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the hashed password with the input password
            if (password_verify($user_passwordByLogin, $row['user_password'])) {
                // Check if the user is active
                if ($row['user_type'] == 'user') {
                    if ($row['user_status'] == 'active') {
                        // Successful login, start session and redirect
                        $_SESSION['user_name'] = $row['user_name']; // Store full name
                        $_SESSION['user_number'] = $row['user_number']; // Store phone number
                        $_SESSION['user_email'] = $user_emailByLogin;
                        $_SESSION['user_type'] = "user";
                        $_SESSION['auth_id'] = $row['user_id'];
                        if (!isset($_SESSION['csrf_token'])) {
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        }

                        // Redirect after login
                        header("Location:index.php");
                        exit(); // End further execution
                    } else {
                        // User is inactive
                        $login_error = "Your account is inactive. Please contact support.";
                    }
                } else {
                    $login_error = "You are not authorized to login here.";
                }
            } else {
                // Password is incorrect
                $login_error = "Invalid email or password.";
            }
        } else {
            // Email does not exist
            $login_error = "Invalid email or password.";
        }
    } else {
        // Email or password is empty
        $login_error = "Please fill in both email and password.";
    }
}

// At the top, after session_start();
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Sign Up & Login - Room Finder Nepal</title>
    <link rel="stylesheet" href="admin/login.css">
    <style>
        /* Move popups to top right and make responsive */
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
    <?php require('helperFunction/SweetAlert.php'); ?>
</head>

<body>
    <script>
        var showSignup = <?php echo $show_signup ? 'true' : 'false'; ?>;
    </script>
    <?php $flash_error = get_flash('error'); if ($flash_error): ?>
        <div class="danger-notify" id="flash-danger"><span><?php echo $flash_error; ?></span></div>
    <?php endif; ?>
    <?php $flash_success = get_flash('success'); if ($flash_success): ?>
        <div class="success-notify" id="flash-success"><span><?php echo $flash_success; ?></span></div>
    <?php endif; ?>
    
    <div class="auth-wrapper" style="background-image: url('admin/uploads/67ae0a34aac00_backgroundLogin.png')">
        <div class="auth-container">
            <!-- Toggle Button -->
            <div class="toggle-container">
                <button id="toggle-button" class="toggle-button">Go to Sign Up</button>
            </div>

            <!-- Glassmorphism Forms -->
            <div id="login-form" class="form-card glass visible">
                <h1>Welcome Back</h1>
                <p style="text-align: center; color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.875rem;">
                    Sign in to your account to continue
                </p>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="userEmailByLogin" value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin) : ""; ?>" placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <div class="password-container">
                            <input type="password" id="login-password" name="userPasswordByLogin" value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin) : ""; ?>" placeholder="Enter your password" required>
                            <button type="button" class="eye-button" onclick="togglePasswordVisibility('login-password', this)">&#128065;</button>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-button" name="login">
                        <span>Sign In</span>
                    </button>
                </form>
            </div>

            <div id="signup-form" class="form-card glass">
                <h1>Create Account</h1>
                <p style="text-align: center; color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.875rem;">
                    Join us and find your perfect room
                </p>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form" id="register-form">
                    <div class="form-group">
                        <label for="user_name">Full Name<span class="required">*</span></label>
                        <input type="text" id="user_name" name="user_name" value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ""; ?>" required placeholder="Enter your full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_email">Email Address<span class="required">*</span></label>
                        <input type="email" id="user_email" name="user_email" value="<?php echo isset($user_email) ? htmlspecialchars($user_email) : ""; ?>" required placeholder="Enter your email address">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_number">Phone Number<span class="required">*</span></label>
                        <input type="tel" id="user_number" name="user_number" value="<?php echo isset($user_number) ? htmlspecialchars($user_number) : ""; ?>" required placeholder="Enter your phone number (9xxxxxxxxx)">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_location">Location</label>
                        <input type="text" id="user_location" name="user_location" value="<?php echo isset($user_location) ? htmlspecialchars($user_location) : ""; ?>" placeholder="Enter your location (optional)">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_password">Password<span class="required">*</span></label>
                        <div class="password-container">
                            <input type="password" id="user_password" name="user_password" required placeholder="Create a strong password">
                            <button type="button" id="toggle-password" class="eye-button">&#128065;</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password<span class="required">*</span></label>
                        <div class="password-container">
                            <input id="confirmPassword" type="password" name="user_confirmation_password" placeholder="Confirm your password">
                            <button type="button" id="toggle-confirm-password" class="eye-button">&#128065;</button>
                        </div>
                    </div>
                    
                    <input type="text" name="register" hidden value="newRegistration">
                    
                    <div id="password-message">
                        <strong>Password Requirements:</strong><br>
                        • Minimum 8 characters<br>
                        • At least 1 uppercase letter<br>
                        • At least 1 number<br>
                        • At least 1 special character
                    </div>
                    
                    <button type="submit" name="register" class="submit-button">
                        <span>Create Account</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Single Popup Notification Container -->
    <div id="popup-notify" class="danger-notify" style="display:none;"><span id="popup-message"></span></div>

    <script src="admin/login.js"></script>
    <script>
        // Additional utility function for login form password toggle
        function togglePasswordVisibility(fieldId, button) {
            const field = document.getElementById(fieldId);
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            button.innerHTML = isPassword ? '&#128065;&#8205;&#127787;' : '&#128065;';
            
            // Add visual feedback
            button.style.transform = 'scale(1.1)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 150);
        }

        // Enhanced form validation with better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to submit buttons
            const submitButtons = document.querySelectorAll('.submit-button');
            submitButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!this.classList.contains('loading')) {
                        this.classList.add('loading');
                        const originalText = this.querySelector('span').textContent;
                        this.querySelector('span').textContent = 'Processing...';
                        
                        // Reset after 3 seconds if form doesn't submit
                        setTimeout(() => {
                            if (this.classList.contains('loading')) {
                                this.classList.remove('loading');
                                this.querySelector('span').textContent = originalText;
                            }
                        }, 3000);
                    }
                });
            });

            // Add smooth scrolling for better mobile experience
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    if (window.innerWidth <= 480) {
                        setTimeout(() => {
                            this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 300);
                    }
                });
            });

            // Add keyboard navigation support
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                    const form = e.target.closest('form');
                    if (form) {
                        const submitButton = form.querySelector('button[type="submit"]');
                        if (submitButton && !submitButton.classList.contains('loading')) {
                            submitButton.click();
                        }
                    }
                }
            });

            // Ensure flash popups are visible for 5 seconds
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

            // Show signup form if registration failed
            if (typeof showSignup !== 'undefined' && showSignup) {
                document.getElementById('login-form').classList.remove('visible');
                document.getElementById('signup-form').classList.add('visible');
            }

            // Show popup notification for errors/success
            var popup = document.getElementById('popup-notify');
            var popupMsg = document.getElementById('popup-message');
            var msg = '';
            var type = 'danger';
            <?php
            $popup_message = '';
            $popup_type = 'danger';
            if (isset($login_error) && $login_error) {
                $popup_message = $login_error;
                $popup_type = 'danger';
            } elseif (isset($form_error) && $form_error) {
                $popup_message = $form_error;
                $popup_type = 'danger';
            } elseif ($flash_error) {
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
