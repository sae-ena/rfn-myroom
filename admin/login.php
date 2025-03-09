<?php
session_start();
require('dbConnect.php');
require('../helperFunction/SweetAlert.php');
// Initialize variables for login data
$user_emailByLogin = "";
$user_passwordByLogin = "";
// $login_error = "";

// Define variables to store form data and errors
$form_error = null;

if (isset($_SESSION['user_email'])) {
    header("Location:dashboard.php");
    exit(); // Ensure no further code is executed after the redirection
}

$formQuery = "SELECT * FROM form_managers WHERE form_slug = ? AND status = ?";
$formSlug = "login_admin";
$formManagerStatus = 1;
$FormSmt = $conn->prepare($formQuery);
$FormSmt->bind_param("si", $formSlug ,$formManagerStatus);
$FormSmt->execute();
$formManagerResult = $FormSmt->get_result();




// Check if the login form is submitted
if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['login'])) {

    $outerIndex = 0;
    foreach ($_POST as $key => $value) {
        $requestArray[$outerIndex] = $value;
        $outerIndex ++;
        
    }
      
    // Get the input data from the form
    $user_emailByLogin = $_POST['userEmailByLogin'];
    $user_passwordByLogin = $_POST['userPasswordByLogin'];

    // Check if email and password are not empty
    if (!empty($user_emailByLogin) && !empty($user_passwordByLogin)) {
        // Query to check if the user email exists in the database
        $query = "SELECT user_id, user_password, user_status ,user_type FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
        $result = $conn->query($query);

        // Check if the query returned a result (i.e., the email exists)
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the hashed password with the input password
            if (password_verify($user_passwordByLogin, $row['user_password'])) {
                // Check if the user is active

                if ($row['user_status'] == 'active') {

                    if($row['user_type'] == "admin"){

                    
                    $_SESSION['user_name'] = $row['user_name'];
                    $_SESSION['user_email'] = $user_emailByLogin;
                    $_SESSION['user_type'] === "admin";

                    
                    header("Location:dashboard.php");
                    exit();
                    }
                    else{
                        $login_error = "Unauthorized.";
                    }

                } else {
                    // User is inactive
                    $login_error = "Your account is inactive. Please contact support.";
                }
            } else {
                // Password is incorrect
                $login_error = "Invalid password.";
            }
        } else {
            // Email does not exist
            $login_error = "Invalid email address.";
        }
    } else {
        // Email or password is empty
        $login_error = "Please fill in both email and password.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
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
    } elseif (isset($user_email)) {
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
                $query = "INSERT INTO users (user_name, user_email, user_number, user_location, user_status, user_password,user_type)
                  VALUES ('$user_name', '$user_email', '$user_number', '$user_location', '$user_status', '$hashed_password','admin')";

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
    <link rel="stylesheet" href="login.css">
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
    <?php
            if ($formManagerResult->num_rows > 0) {
                $formData = $formManagerResult->fetch_assoc();

                echo '<div class="auth-wrapper"
                style="background-image: url(\'/admin/'.$formData["background_image"].'\');
                       background-color: ' . $formData["background_color"] . ' !important;
                       background-size: cover;
                       background-repeat: no-repeat;
                       background-position: center center;
                       background-attachment: fixed;
                       width: 100vw;
                       height: 100vh;
                       display: flex;
                       align-items: center;
                       justify-content: center;">';
        
        
        ?>
        <div class="auth-container">
            <!-- Toggle Button -->
            <!-- <div class="toggle-container">
                <button id="toggle-button" class="toggle-button">Go to Sign Up</button>
            </div> -->

            <!-- Glassmorphism Forms -->
            <div id="login-form" class="form-card glass visible">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<?php
   
    
    
    // Decode the field details (it's a JSON string)
    $fields = json_decode($formData['field_detail'], true); // true converts it to an associative array

    if (!empty($fields)) {
            echo '<h1>' . htmlspecialchars($formData['form_name']) . '</h1>';
        echo '<form action="submit_form.php" method="POST">';
        $innerIndex = 0;
        foreach ($fields as $index => $field) {

       
            echo '<label for="' . htmlspecialchars($field["name"]) . '">' . htmlspecialchars($field['label']) ;  if($field['required'] == true){ echo'<span class="required" style="
    color: red;
    font-size: 16px;
    margin-left: 5px;
">*</span>';};  echo'</label>';


if ($field['type'] == 'select') {
    echo '<select name="'.$field["name"].$index.'" id="field_' . $index . '" ' . ($field['required'] ? 'required' : '') . '>';
    foreach ($field['options'] as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
} else {
    
    echo '<input type="' . htmlspecialchars($field['type']) . '" 
           name="'.$field['name'].'" value = "'.(isset($requestArray[$innerIndex]) && $requestArray[$innerIndex]?$requestArray[$innerIndex]:"").'"
           id="field_' . $index . '" 
           placeholder="' . htmlspecialchars($field['placeholder']) . '" 
           ' . ($field['required'] ? 'required' : '') . '>';
}
$innerIndex++;
            
        }
     
    } echo '<button type="submit" class="submit-button" name="login">Login</button>

    </form>
</div>';
}
else{
    ?>
<div id="formNotFound" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background-color: rgba(0, 0, 0, 0.6); z-index: 1000;">
    
    <div style="position: relative; margin: 15% auto; background-color: #ffebee; padding: 25px; width: 350px; 
        border-radius: 10px; text-align: center; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); 
        border: 2px solid #d32f2f; animation: fadeIn 0.4s ease-in-out;">
        
        <!-- Error Icon -->
        <div style="width: 60px; height: 60px; background-color: #d32f2f; color: white; 
            font-size: 30px; font-weight: bold; line-height: 60px; text-align: center; 
            border-radius: 50%; margin: -50px auto 10px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);">
            !
        </div>

        <h3 style="color: #b71c1c; margin-bottom: 10px; font-family: Arial, sans-serif;">Error</h3>

        <hr style="border: 2px solid #d32f2f; width: 100%;">

            <p id="errorMessage" style="color: #333; font-size: 16px; font-family: Arial, sans-serif; 
                margin: 15px 0; padding: 10px; background: #ffcdd2; border-radius: 5px; box-shadow: inset 0px 1px 4px rgba(0,0,0,0.1);">
                Form not found. Please contact the administrator.
            </p>


        <!-- Close Button -->
        <button onclick="document.getElementById('formNotFound').style.display='block'" 
            style="background-color: #d32f2f; color: white; border: none; padding: 10px 20px; 
            font-size: 14px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2); transition: 0.3s;">
            Close
        </button>

    </div>
</div>

    <?php

}
?>

           

               
                    <!-- <label for="login-email">Email:</label>
                     <input type="email" id="login-email" required>
                    <input type="text" name="userEmailByLogin"
                        value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin) : ""; ?>"
                        placeholder="Enter your email" required>
                    <label for="login-password">Password:</label>
                  <input type="password" id="login-password" required> 
                    <input type="password" name="userPasswordByLogin"
                        value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin) : ""; ?>"
                        placeholder="Enter your password" required> -->
                  

            <div id="signup-form" class="form-card glass ">
                <h1>Sign Up</h1>
                <!-- Form submits to the same page -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form">

                    <label for="user_name">Full Name</label>
                    <input type="text" id="user_name" name="user_name"
                        value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ""; ?>" required
                        placeholder="Enter your name">

                    <label for="user_email">Email</label>
                    <input type="email" id="user_email" name="user_email"
                        value="<?php echo isset($user_email) ? htmlspecialchars($user_email) : ""; ?>" required
                        placeholder="Enter your email">

                    <label for="user_number">Phone Number</label>
                    <input type="number" id="user_number" name="user_number"
                        value="<?php echo isset($user_number) ? htmlspecialchars($user_number) : ""; ?>" required
                        placeholder="Enter your phone number">

                    <label for="user_location">Location</label>
                    <input type="text" id="user_location" name="user_location"
                        value="<?php echo isset($user_location) ? htmlspecialchars($user_location) : ""; ?>"
                        placeholder="Enter your location (optional)">

                    <label for="user_status">Status</label>
                    <select id="user_status" name="user_status" required>
                        <option value="active" <?php echo isset($user_status) ? ($user_status == 'active') ? 'selected' : '' : ""; ?>>
                            Active
                        </option>
                        <option value="inActive" <?php echo isset($user_status) ? ($user_status == 'inActive') ? 'selected' : '' : ""; ?>>
                            Inactive</option>
                    </select>

                    <label for="user_password">Password</label>
                    <input type="password" id="user_password" name="user_password" required
                        placeholder="Enter a password">

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
    </script>
</body>

</html>