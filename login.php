<?php
// Start the session at the very top
session_start();

require('admin/dbConnect.php');
require('helperFunction/InsertRoomData.php');

// Initialize variables for login data
$user_emailByLogin = "";
$user_passwordByLogin = "";
$form_error = null;

// If the user is already logged in, redirect to index page
if (isset($_SESSION['user_email'])) {
    header("Location:index.php");
    exit(); // Ensure no further code is executed after the redirection
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Sign Up & Login - Room Finder Nepal</title>
    <link rel="stylesheet" href="admin/login.css">
    <?php require('helperFunction/SweetAlert.php'); ?>
</head>

<body>
    <?php if (isset($login_error)): ?>
        <div class="danger-notify">
            <span><?php echo $login_error; ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($successfullyRegister)): ?>
        <div class="success-notify">
            <span><?php echo $successfullyRegister; ?></span>
        </div>
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

    <!-- Enhanced Alert Box for Validation Messages -->
    <div id="alert-box" class="alert-box">
        <div class="alert-message" id="alert-message"></div>
    </div>

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
        });
    </script>
</body>

</html>
