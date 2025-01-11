<?php
session_start(); 
require('dbConnect.php');
// Initialize variables for login data
$user_emailByLogin = "";
$user_passwordByLogin = "";
// $login_error = "";

// Define variables to store form data and errors
$form_error = null;

if (isset($_SESSION['user_email'])) {
        header("Location: dashboard.php");
        exit(); // Ensure no further code is executed after the redirection
    }       


// Check if the login form is submitted
if (($_SERVER['REQUEST_METHOD'] === 'POST' )&& isset($_POST['login'])) {
    // Get the input data from the form
    $user_emailByLogin = $_POST['userEmailByLogin'];
    $user_passwordByLogin = $_POST['userPasswordByLogin'];

    // Check if email and password are not empty
    if (!empty($user_emailByLogin) && !empty($user_passwordByLogin)) {
        // Query to check if the user email exists in the database
        $query = "SELECT user_id, user_password, user_status FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
        $result = $conn->query($query);

        // Check if the query returned a result (i.e., the email exists)
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the hashed password with the input password
            if (password_verify($user_passwordByLogin, $row['user_password'])) {
                // Check if the user is active
               
                if ($row['user_status'] == 'active') {
                    // Successful login, start session and redirect
                    session_start();
                    $_SESSION['user_name'] = $row['user_name'];
                    $_SESSION['user_email'] = $user_emailByLogin;
                    $_SESSION['user_type'] === "admin";

                    // Redirect to the dashboard or main page (replace 'dashboard.php' with the actual destination)
                    header("Location: dashboard.php");
                    
                } else {
                    // User is inactive
                    $login_error = "Your account is inactive. Please contact support.";
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
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $user_name = $user_email = $user_number = $user_location = $user_status = $user_password = "";
    // Get form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_number = $_POST['user_number'];
    $user_location = $_POST['user_location'];
    $user_status = $_POST['user_status'];
    $user_password = $_POST['user_password'];

    // Validate input
    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        $form_error = "Please fill all required fields.";
    }
    elseif(isset($user_email)){
        $uniqueCheck = "SELECT user_email FROM users WHERE user_email = '$user_email' ";
        $result = $conn->query($uniqueCheck);
        if($result->num_rows > 0){
    $form_error = "Email already exists";

}
    elseif(isset($user_number)){
        $uniqueCheck = "SELECT user_number FROM users WHERE user_number = '$user_number' ";
        $result = $conn->query($uniqueCheck);
        if($result->num_rows > 0){
    $form_error = "PhoneNumber already exists";

}
elseif(! str_contains($user_email,'@'))  $form_error = "Invalid Email Format";
elseif (strlen($user_number) != 10 || !str_starts_with($user_number,'98')) {
    $form_error = "Invalid PhoneNumber.";
}
elseif (strlen($user_password) < 5) {
    $form_error = "Password must be at least 5 characters long.";
}
else{

        // Hash the password
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert data into the 'users' table
        $query = "INSERT INTO users (user_name, user_email, user_number, user_location, user_status, user_password,user_type)
                  VALUES ('$user_name', '$user_email', '$user_number', '$user_location', '$user_status', '$hashed_password','admin')";

        if ($conn->query($query) === TRUE) {
           $successfullyRegister = "User registered successfully!";
        } else {
            $form_error = "Please try again.";
        }

        // Close the database connection
        $conn->close();
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
    <link rel="stylesheet" href="login.css">
    <?php 
require('../helperFunction/SweetAlert.php');?>
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
    <div class="auth-wrapper" style="background-image: url('uploads/RealEstateAgentvs.MortgageBrokerWhatstheDifference-6260ea50d1044056899d8cf6dff7d47d.jpg')" >
        <div class="auth-container">
            <!-- Toggle Button -->
            <div class="toggle-container">
                <button id="toggle-button" class="toggle-button">Go to Sign Up</button>
            </div>

            <!-- Glassmorphism Forms -->
            <div id="login-form" class="form-card glass visible">
                <h1>Login</h1>
                
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
                    <label for="login-email">Email:</label>
                    <!-- <input type="email" id="login-email" required> -->
                    <input type="text" name="userEmailByLogin" value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin):""; ?>" placeholder="Enter your email" required>
                    <label for="login-password">Password:</label>
                    <!-- <input type="password" id="login-password" required> -->
                    <input type="password" name="userPasswordByLogin" value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin):""; ?>" placeholder="Enter your password" required>
                    <button type="submit" class="submit-button" name="login">Login</button>
                
                </form>
            </div>

            <div id="signup-form" class="form-card glass ">
                <h1>Sign Up</h1>
                 <!-- Form submits to the same page -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form">
    
                <label for="user_name">Full Name</label>
                <input type="text" id="user_name" name="user_name" value="<?php echo isset($user_name) ?  htmlspecialchars($user_name):""; ?>" required placeholder="Enter your name">
                
                <label for="user_email">Email</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo isset($user_email) ? htmlspecialchars($user_email):""; ?>" required placeholder="Enter your email">
    
                <label for="user_number">Phone Number</label>
                <input type="number" id="user_number" name="user_number" value="<?php echo isset($user_number)? htmlspecialchars($user_number):""; ?>" required placeholder="Enter your phone number">
    
                <label for="user_location">Location</label>
                <input type="text" id="user_location" name="user_location" value="<?php echo  isset($user_location) ?htmlspecialchars($user_location):""; ?>" placeholder="Enter your location (optional)">
    
                <label for="user_status">Status</label>
                <select id="user_status" name="user_status" required>
                    <option value="active" <?php echo isset($user_status)? ($user_status == 'active') ? 'selected' : '':""; ?>>Active</option>
                    <option value="inActive" <?php echo isset($user_status)? ($user_status == 'inActive') ? 'selected' : '':""; ?>>Inactive</option>
                </select>
    
                <label for="user_password">Password</label>
                <input type="password" id="user_password" name="user_password" required placeholder="Enter a password">
    
                <button type="submit" name="register" class="submit-button">Register</button>
            </form>
        </div>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
    <script>
    // If PHP successfully registered the user, we delay redirection by 3 seconds
    <?php if (isset($successfullyRegister)): ?>
        setTimeout(function() {
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
</script>
</body>
</html>
