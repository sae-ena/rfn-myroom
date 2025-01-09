<?php
session_start(); 
require('admin/dbConnect.php');
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
        $query = "SELECT user_id, user_password, user_status ,user_type FROM users WHERE user_email = '$user_emailByLogin' LIMIT 1";
        $result = $conn->query($query);

        // Check if the query returned a result (i.e., the email exists)
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the hashed password with the input password
            if (password_verify($user_passwordByLogin, $row['user_password'])) {
                // Check if the user is active
               
                if ($row['user_status'] == 'active') {
                    // Successful login, start session and redirect
                    if($row['user_type'] == 'user'){
                       
                        session_start();
                        $_SESSION['auth_id'] = $row['user_id'];
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['user_email'] = $user_emailByLogin;
                        $_SESSION['user_type'] = $row['user_type'];
                        
                        // Redirect to the dashboard or main page (replace 'dashboard.php' with the actual destination)
                        header("Location: index.php");
                    }
                    else{
                        $form_error = "You are not authorized to login here.";
                    }
                    
                } else {
                    // User is inactive
                    $form_error = "Your account is inactive. Please contact support.";
                }
            } else {
                // Password is incorrect
                $form_error = "Invalid email or password.";
            }
        } else {
            // Email does not exist
            $form_error = "Invalid email or password.";
        }
    } else {
        // Email or password is empty
        $form_error = "Please fill in both email and password.";
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $user_name = $user_email = $user_number = $user_password = $user_confirm_password="";
    // Get form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_number = $_POST['user_number'];
    $user_password = $_POST['user_password'];
    $user_confirm_password = $_POST['user_confirmation_password'];

    // Validate input
    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        $form_error = "Please fill all required fields.";
    }
    elseif(isset($user_email)){
        $uniqueCheck = "SELECT user_email FROM users WHERE user_email = '$user_email'";
        $result = $conn->query($uniqueCheck);
        if($result->num_rows > 0){
    $form_error = "Email already exists";

}
elseif (strlen($user_number) < 5) {
    $form_error = "Number must be at least 5 characters long.";
}
elseif (strlen($user_password) < 5) {
    $form_error = "Password must be at least 5 characters long.";
}
elseif($user_password !== $user_confirm_password) $form_error = "Confirmation Password didnot match .";
else{

        // Hash the password
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert data into the 'users' table
        $query = "INSERT INTO users (user_name, user_email, user_number,  user_password)
                  VALUES ('$user_name', '$user_email', '$user_number', '$hashed_password')";

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
?>

<link rel="stylesheet" href="admin/login.css">
<body style="">

<h2 id="userLogin">Casabo Room Finder</h2>
  <!-- Left Side: Login -->
  <div style="width: 35%; background-color:rgb(44, 104, 164); height: 50vh;display: flex; justify-content: center; align-items: center; margin: 1px 20px; padding: 0; border-radius: 54px 21px;">
    <div style="color: white; text-align: center;height:60%; width: 80%; max-width: 400px;">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; flex-direction: column; gap: 15px;border:2px solid white; padding: 20px; border-radius: 10px;">
          <h2>Login</h2>
        <input type="text" id="login" name="userEmailByLogin" value="<?php echo isset($user_emailByLogin) ? htmlspecialchars($user_emailByLogin):""; ?>"  placeholder="Username" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="password" name="userPasswordByLogin" value="<?php echo isset($user_passwordByLogin) ? htmlspecialchars($user_passwordByLogin):""; ?>" placeholder="Password" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <button type="submit" name="login" style="padding: 10px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Login</button>
        <p style="color: white; font-size: 14px;">Don't have an account? <a href="#signup" style="color: #ecf0f1; text-decoration: none;">Sign up</a></p>
      </form>
    </div>
  </div>

  <!-- Right Side: Signup -->
  <div style="width: 40%; background-color:#60BB46; display: flex; justify-content: center; align-items: center; margin: 1px 39px; padding: 0; border-radius: 54px 21px;">
    <div style="text-align: center; width: 80%; max-width: 400px; margin: 0; padding: 20px; border-radius: 10px;">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: flex; flex-direction: column; gap: 15px; " >
      <h2>Sign Up</h2>
        <input type="text" name="user_name" value="<?php echo isset($user_name) ?  htmlspecialchars($user_name):""; ?>" placeholder="Full Name" id="signup" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="email" name="user_email"  value="<?php echo isset($user_email) ? htmlspecialchars($user_email):""; ?>" placeholder="Email Address" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="text" name="user_number" value="<?php echo isset($user_number)? htmlspecialchars($user_number):""; ?>" placeholder="+977 984120302" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="password" name="user_password" placeholder="Password" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="password" name="user_confirmation_password" placeholder="Confirm Password" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <button type="submit" name="register" style="padding: 10px; background-color:rgb(200, 71, 71); color: white; border: none; border-radius: 5px; cursor: pointer;">Sign Up</button>
        <p style="font-size: 14px;">Already have an account? <a href="#login" style="color:rgb(0, 3, 5); text-decoration: none;">Login</a></p>
      </form>
    </div>
  </div>
 <?php
 require('helperFunction/SweetAlert.php');
 ?>
        
    </body>
    </html>