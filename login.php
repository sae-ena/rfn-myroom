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
        $query = "SELECT user_id, user_password, user_type, user_status, user_name FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
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
                        $_SESSION['user_name'] = explode(' ', trim($row['user_name']))[0];
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
    <title>Modern Sign Up & Login</title>
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
                <h1>Login</h1>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <label for="login-email">Email:</label>
                    <input type="text" name="userEmailByLogin" value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin) : ""; ?>" placeholder="Enter your email" required>
                    <label for="login-password">Password:</label>
                    <input type="password" name="userPasswordByLogin" value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin) : ""; ?>" placeholder="Enter your password" required>
                    <button type="submit" class="submit-button" name="login">Login</button>
                </form>
            </div>

            <div id="signup-form" class="form-card glass ">
                <h1>Sign Up</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form" id="register-form">
                    <label for="user_name">Full Name<span class="required">*</span></label>
                    <input type="text" id="user_name" name="user_name" value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ""; ?>" required placeholder="Enter your name">
                    <label for="user_email">Email<span class="required">*</span></label>
                    <input type="email" id="user_email" name="user_email" value="<?php echo isset($user_email) ? htmlspecialchars($user_email) : ""; ?>" required placeholder="Enter your email">
                    <label for="user_number">Phone Number<span class="required">*</span></label>
                    <input type="number" id="user_number" name="user_number" value="<?php echo isset($user_number) ? htmlspecialchars($user_number) : ""; ?>" required placeholder="Enter your phone number">
                    <label for="user_location">Location</label>
                    <input type="text" id="user_location" name="user_location" value="<?php echo isset($user_location) ? htmlspecialchars($user_location) : ""; ?>" placeholder="Enter your location (optional)">
                    <label for="user_password">Password<span class="required">*</span></label>
                    <div class="password-container">
                        <input type="password" id="user_password" name="user_password" required placeholder="Enter a password">
                        <button type="button" id="toggle-password" class="eye-button">&#128065;</button>
                    </div>
                    <label for="confirmPassword">Confirm Password<span class="required">*</span></label>
                    <div class="password-container">
                        <input id="confirmPassword" type="password" name="user_confirmation_password" placeholder="Confirm Password">
                        <button type="button" id="toggle-confirm-password" class="eye-button">&#128065;</button>
                    </div>
                    <input type="text" name="register" hidden id="" value="newRegistration">
                    <p style="color: white; background-color: rgba(244, 67, 54, 0.6); font-size: 12px; border-radius: 25px; padding: 10px 15px; margin: 0px 0px; display: none;" id="password-message">
                        *Password: Min 8 chars, 1 uppercase, 1 number, 1 symbol.
                    </p>
                    <button type="submit" name="register" class="submit-button">Register</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom validation Alert || Client SIDE -->
    <div id="alert-box" class="alert-box">
        <span id="alert-message"></span>
    </div>

    <script src="admin/login.js"></script>
    <script>
        // Password toggle functionality for both fields
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('user_password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            const confirmPasswordField = document.getElementById('confirmPassword');
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
            } else {
                confirmPasswordField.type = 'password';
            }
        });

        // Form validation before submitting
        document.getElementById('register-form').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent form submission to check validation first

            const fullName = document.getElementById('user_name').value;
            const email = document.getElementById('user_email').value;
            const phoneNumber = document.getElementById('user_number').value;
            const password = document.getElementById('user_password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            let valid = true;

            // Validate Full Name
            if (!/^[A-Za-z\s]+$/.test(fullName)) {
                showAlert("Full name must only contain letters and spaces.");
                valid = false;
            }

            // Validate Email
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                showAlert("Please enter a valid email address.");
                valid = false;
            }

            // Validate Phone Number
            if (!/^[98|97]\d{9}$/.test(phoneNumber)) {
                showAlert("Phone number must be 10 digits and start with 98,97.");
                valid = false;
            }

            // Validate Password
            const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
            if (!passwordPattern.test(password)) {
                showAlert("Password must be at least 8 characters long, contain one uppercase letter, one number, and one symbol.");
                valid = false;
            }

            // Confirm Password match
            if (password !== confirmPassword) {
                showAlert("Passwords do not match.");
                valid = false;
            }

            if (valid) {
                // Proceed with form submission if everything is valid
                document.getElementById('register-form').submit();
            }
        });

        // Display alert message
        function showAlert(message) {
            document.getElementById('alert-message').innerText = message;
            document.getElementById('alert-box').style.display = 'block';
        }
    </script>
</body>

</html>
