<?php
require('dbConnect.php');
// Define variables to store form data and errors
$user_name = $user_email = $user_number = $user_location = $user_status = $user_password = "";
$form_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $uniqueCheck = "SELECT user_email FROM users WHERE user_email = '$user_email'";
        $result = $conn->query($uniqueCheck);
        if($result->num_rows > 0){
    $email_error = "Email already exists";

}
else{

        // Hash the password
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert data into the 'users' table
        $query = "INSERT INTO users (user_name, user_email, user_number, user_location, user_status, user_password)
                  VALUES ('$user_name', '$user_email', '$user_number', '$user_location', '$user_status', '$hashed_password')";

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="register-container">
        <h2>Create an Account</h2>
      
        
        <!-- Form submits to the same page -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register-form">
        <?php if (isset($email_error)): ?>
        <div class="danger-notify">
            <span><?php echo $email_error; ?></span>
        </div>
    <?php endif; ?>
        <?php if (isset($successfullyRegister)): ?>
        <div class="success-notify">
            <span><?php echo $successfullyRegister; ?></span>
        </div>
    <?php endif; ?>
            
            <?php if ($form_error): ?>
                <p style="color: red;"><?php echo $form_error; ?></p>
            <?php endif; ?>

            <label for="user_name">Full Name</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>" required placeholder="Enter your name">
            
            <label for="user_email">Email</label>
            <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>" required placeholder="Enter your email">

            <label for="user_number">Phone Number</label>
            <input type="number" id="user_number" name="user_number" value="<?php echo htmlspecialchars($user_number); ?>" required placeholder="Enter your phone number">

            <label for="user_location">Location</label>
            <input type="text" id="user_location" name="user_location" value="<?php echo htmlspecialchars($user_location); ?>" placeholder="Enter your location (optional)">

            <label for="user_status">Status</label>
            <select id="user_status" name="user_status" required>
                <option value="active" <?php echo ($user_status == 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inActive" <?php echo ($user_status == 'inActive') ? 'selected' : ''; ?>>Inactive</option>
            </select>

            <label for="user_password">Password</label>
            <input type="password" id="user_password" name="user_password" required placeholder="Enter a password">

            <button type="submit" class="submit-btn">Register</button>
        </form>
    </div>
</body>
<script>
    // If PHP successfully registered the user, we delay redirection by 3 seconds
    <?php if (isset($successfullyRegister)): ?>
        setTimeout(function() {
            window.location.href = 'login.html'; 
        }, 600);
    <?php endif; ?>
</script>
</html>
