<style>
    .alert-box {
    position: fixed;
    top: 20px;
    right: 2px;
    padding: 15px;
    background-color:rgb(243, 62, 12);
    color: white;
    display: none; /* Initially hidden */
    font-size: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    border-radius: 29px;
    border-radius: 10px 0px 10px 30px;
}

.alert-box.show {
    display: block;
}
.required {
    color: red;
    font-size: 16px;
    margin-left: 5px;
}
.password-container {
    position: relative;
    display: inline-block;
    width: 100%;
}
input[type="password"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
}

/* Style for the eye button */
.eye-button {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 20px;
    color: #333;
}
</style>
<?php
session_start();
require('admin/dbConnect.php');
require('helperFunction/InsertRoomData.php');
// Initialize variables for login data
$user_emailByLogin = "";
$user_passwordByLogin = "";
// $login_error = "";

// Define variables to store form data and errors
$form_error = null;

if (isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit(); // Ensure no further code is executed after the redirection
}


// Check if the login form is submitted
if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['login'])) {
    // Get the input data from the form
    $user_emailByLogin = $_POST['userEmailByLogin'];
    $user_passwordByLogin = $_POST['userPasswordByLogin'];

    // Check if email and password are not empty
    if (!empty($user_emailByLogin) && !empty($user_passwordByLogin)) {
        // Query to check if the user email exists in the database
        $query = "SELECT user_id, user_password,user_type, user_status ,user_name FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
        $result = $conn->query($query);

        // Check if the query returned a result (i.e., the email exists)
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the hashed password with the input password
            if (password_verify($user_passwordByLogin, $row['user_password'])) {
                // Check if the user is active

                if($row['user_type'] == 'user'){
                if ($row['user_status'] == 'active') {
                    // Successful login, start session and redirect
                    session_start();
                    $_SESSION['user_name'] = explode(' ', trim($row['user_name']))[0];
                    $_SESSION['user_email'] = $user_emailByLogin;
                    $_SESSION['user_type'] = "user";
                    $_SESSION['auth_id'] = $row['user_id'];
                    if (!isset($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    // Redirect to the dashboard or main page (replace 'dashboard.php' with the actual destination)
                    header("Location: index.php");

                } else {
                    // User is inactive
                    $login_error = "Your account is inactive. Please contact support.";
                }
            }
            else{
                $form_error = "You are not authorized to login here.";
                
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $user_name = $user_email = $user_number = $user_location = $user_confirm_password = $user_password = "";
    // Get form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_number = $_POST['user_number'];
    $user_location = $_POST['user_location'];
    $user_password = $_POST['user_password'];
    $user_confirm_password = $_POST['user_confirmation_password'];

    // Validate input
    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        $form_error = "Please fill all required fields.";
    }
    if ($user_password !== $user_confirm_password)
        $form_error = "Confirmation Password didnot match .";
    elseif (isset($user_email)) {
        $uniqueCheck = "SELECT user_email FROM users WHERE user_email = '$user_email' ";
        $result = $conn->query($uniqueCheck);
        if ($result->num_rows > 0) {
            $form_error = "Email already exists";

        } elseif (isset($user_number)) {
            $uniqueCheck = "SELECT user_number FROM users WHERE user_number = '$user_number' ";
            $result = $conn->query($uniqueCheck);
            if ($result->num_rows > 0) {
                $form_error = "PhoneNumber already exists";

            } elseif (!str_contains($user_email, '@'))
                $form_error = "Invalid Email Format";
            elseif (strlen($user_number) != 10 || !str_starts_with($user_number, '98')) {
                $form_error = "Invalid PhoneNumber.";
            } elseif (strlen($user_password) < 5) {
                $form_error = "Password must be at least 5 characters long.";
            } else {

                // Hash the password
                $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

                // Prepare the SQL query to insert data into the 'users' table
                $query = "INSERT INTO users (user_name, user_email, user_number, user_location, user_password,user_type)
                  VALUES ('$user_name', '$user_email', '$user_number', '$user_location', '$hashed_password','user')";

                $sqlResult = InsertRoomData::insertData($query);
                $successfullyRegister = "User registered successfully!";



            }
        }
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
    <?php
    require('helperFunction/SweetAlert.php'); ?>
</head>

<body>
    <?php if (isset($email_error)): ?>
        <div class="danger-notify">
            <span><?php echo $email_error; ?></span>
        </div>
    <?php endif; ?>
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
    <div class="auth-wrapper"
        style="background-image: url('admin/uploads/67ae0a34aac00_backgroundLogin.png')">
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
                    <!-- <input type="email" id="login-email" required> -->
                    <input type="text" name="userEmailByLogin"
                        value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin) : ""; ?>"
                        placeholder="Enter your email" required>
                    <label for="login-password">Password:</label>
                    <!-- <input type="password" id="login-password" required> -->
                    <input type="password" name="userPasswordByLogin"
                        value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin) : ""; ?>"
                        placeholder="Enter your password" required>
                    <button type="submit" class="submit-button" name="login">Login</button>

                </form>
            </div>

            <div id="signup-form" class="form-card glass ">
                <h1>Sign Up</h1>
                <!-- Form submits to the same page -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form"  id="register-form">

                    <label for="user_name">Full Name<span class="required">*</span></label>
                    <input type="text" id="user_name" name="user_name"
                        value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ""; ?>" required
                        placeholder="Enter your name">

                    <label for="user_email">Email<span class="required">*</span></label>
                    <input type="email" id="user_email" name="user_email"
                        value="<?php echo isset($user_email) ? htmlspecialchars($user_email) : ""; ?>" required
                        placeholder="Enter your email">

                    <label for="user_number">Phone Number<span class="required">*</span></label>
                    <input type="number" id="user_number" name="user_number"
                        value="<?php echo isset($user_number) ? htmlspecialchars($user_number) : ""; ?>" required
                        placeholder="Enter your phone number">

                    <label for="user_location">Location</label>
                    <input type="text" id="user_location" name="user_location"
                        value="<?php echo isset($user_location) ? htmlspecialchars($user_location) : ""; ?>"
                        placeholder="Enter your location (optional)">

                    <label for="user_password">Password<span class="required">*</span></label>
                    <div class="password-container">
                    <input type="password" id="user_password" name="user_password" required
                        placeholder="Enter a password"><button type="button" id="toggle-password" class="eye-button">&#128065;</button>
                        </div>

                    <label for="confirmPassword">Confirm Password<span class="required">*</span></label>
                    <div class="password-container">
                    <input id="confirmPassword" type="password" name="user_confirmation_password"
                        placeholder="Confirm Password"
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"><button type="button" id="toggle-confirm-password" class="eye-button">&#128065;</button>
</div>
                        <p style="color: white; background-color: rgba(244, 67, 54, 0.6); font-size: 12px; border-radius: 25px; padding: 10px 15px; margin: 0px 0px; display: none;" id="password-message">
    *Password: Min 8 chars, 1 uppercase, 1 number, 1 symbol.
</p>


                    <button type="submit" name="register" class="submit-button">Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- Custom validation Alert || Client SIDE -->
<div id="alert-box" class="alert-box">
    <span id="alert-message"></span>
</div>

    <script src="admin/login.js"></script>
    <script>

const passwordField = document.getElementById('user_password');
const confirmPasswordField = document.getElementById('confirmPassword');
const passwordMessage = document.getElementById('password-message');

        passwordField.addEventListener('focus', function() {
    passwordMessage.style.display = 'inline-block'; // Show the message
});

// Show the message when the user focuses on the confirm password field as well
confirmPasswordField.addEventListener('focus', function() {
    passwordMessage.style.display = 'inline-block'; // Show the message
});
document.addEventListener('click', function(event) {
    targetedId = event.target.id;

    if((targetedId !="user_password" && targetedId != "confirmPassword") && (targetedId != "toggle-password" && targetedId != "toggle-confirm-password" )){
        passwordMessage.style.display = 'none';
    }

    
    
});
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
        // If PHP successfully registered the user, we delay redirection by 3 seconds
        <?php if (isset($successfullyRegister)): ?>
            setTimeout(function () {
                window.location.href = 'login.php';
            }, 1000);
        <?php endif; ?>
        <?php if (isset($form_error) && is_string($form_error)): ?>

            function gotoSignup() {
                const signupForm = document.getElementById('signup-form');

                loginForm.classList.remove('visible');
                signupForm.classList.add('visible');
                toggleButton.textContent = "Go to Login";
            }
            gotoSignup();
        <?php endif; ?>
       
        function showAlert(message) {
    const alertBox = document.getElementById('alert-box');
    const alertMessage = document.getElementById('alert-message');
    alertMessage.textContent = message; // Set the error message
    alertBox.classList.add('show'); // Show the alert box
    
    // Hide the alert after 4 seconds
    setTimeout(() => {
        alertBox.classList.remove('show');
    }, 4000);
}

document.getElementById('register-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form submission to check validation first

    
    // Get form values
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

    // Validate Phone Number (must be 10 digits and start with 9, 8, 7, or 6)
    if (!/^[98|97]\d{8}$/.test(phoneNumber)) {
        showAlert("Phone number must be 10 digits and start with 98,97.");
        valid = false;
    }

    // Validate Password (minimum 8 characters, one uppercase letter, one number, and one special character)
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
    if (!passwordPattern.test(password)) {
        showAlert("Password must be at least 8 characters long, include at least one uppercase letter, one number, and one special character.");
        valid = false;
    }

    // Confirm Password match
    if (password !== confirmPassword) {
        showAlert("Password and Confirm Password must match.");
        valid = false;
    }

    // If all validations pass, submit the form
    if (valid) {
        console.log("Form is valid, submitting...");
        this.submit();
    } else {
        console.log("Form validation failed, not submitting.");
    }
});
    </script>
</body>

</html>