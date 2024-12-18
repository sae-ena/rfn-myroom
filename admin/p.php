<?php
// require "leftSidebar.php";
require "dbConnect.php";
$roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription = $roomImage = "";
$roomId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // Sanitize input data using mysqli_real_escape_string (optional if using prepared statements)
    $roomTitle = mysqli_real_escape_string($conn, $_POST['title']);
    $roomLocation = mysqli_real_escape_string($conn, $_POST['location']);
    $roomPrice = floatval($_POST['price']); // Ensuring it's a number
    $roomStatus = mysqli_real_escape_string($conn, $_POST['status']);
    $roomType = mysqli_real_escape_string($conn, $_POST['type']);
    $roomDescription = mysqli_real_escape_string($conn, $_POST['description']);
    if(isset($_POST['enabledEdit'])){

        $EditMode = mysqli_real_escape_string($conn, $_POST['enabledEdit']);
    }
    if ( isset($_FILES['room_image'])) {

        // Get the file details
        $file_name = $_FILES['room_image']['name'];
        $file_tmp = $_FILES['room_image']['tmp_name'];
        $file_size = $_FILES['room_image']['size'];
        $file_error = $_FILES['room_image']['error'];
    
        // Define allowed file types and size limit (e.g., 2MB)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2 * 1024 * 1024;  // 2 MB
    
        // Check if there were any errors
        if ($file_error !== 0) {
            echo "Error: There was an issue uploading the file.";
            exit;
        }
    
        // Validate file size
        if ($file_size > $max_file_size) {
            echo "Error: File size exceeds the maximum limit of 5 MB.";
            exit;
        }
    
        // Validate file type
        if (!in_array(mime_content_type($file_tmp), $allowed_types)) {
            echo "Error: Only JPEG, PNG, and GIF files are allowed.";
            exit;
        }
    
        // Generate a unique name for the file to avoid conflicts
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Create the uploads directory if it doesn't exist
        }
    
        // Generate a unique filename to avoid overwriting
        $new_file_name = uniqid() . '_' . basename($file_name);
        $upload_path = $upload_dir . $new_file_name;
    
        // Move the uploaded file to the desired directory
        move_uploaded_file($file_tmp, $upload_path);
    } 
    

    if(is_numeric($EditMode)){

        
    if (!isset($form_error)) {
        $query = "UPDATE rooms SET room_name = ?, room_location = ?, room_price = ?, room_type = ?, room_status = ?, room_description = ?, room_image = ? WHERE room_id = ?;";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ssdssssi", $roomTitle, $roomLocation, $roomPrice, $roomType, $roomStatus, $roomDescription, $roomImage, $EditMode);
            if ($stmt->execute()) {
                $successfullyRoomAdded = "Rooms information has been updated.";
                $roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription = $roomImage = "";
            } else {
                $form_error = "Error updating room.";
            }
        }
           
    
    }
    }

    


    if (!isset($form_error)) {

        if ($stmt = $conn->prepare($query)) {

            // Bind the variables to the prepared statement
            $stmt->bind_param("ssdssss", $roomTitle, $roomLocation, $roomPrice, $roomType, $roomStatus, $roomDescription, $upload_path);

            // Execute the query
            if ($stmt->execute()) {
                $successfullyRoomAdded = "Rooms has been saved.";

                $roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription = $roomImage = "";

            }

            // Close the statement
            $stmt->close();
        } 
    }
}


?>

<div class="dashboard-content">
    <div class="form-container"
        style="margin-left: 260px; padding: 5px; display: flex; justify-content: center; align-items: center;">
        <form id="room-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <h1>Add New Room Listing</h1>
            <input type="number" hidden  value="<?= htmlspecialchars($roomId) ?>" name="enabledEdit">
            <!-- Room Title -->
            <label for="room-title">Room Title:</label>
            <input type="text" id="room-title" name="title" placeholder="Enter room title"
                value="<?= htmlspecialchars($roomTitle) ?>">

          

            <!-- Upload Photos -->
            <label for="photos">Upload Photos:</label>
            <input type="file" id="photos" name="room_image" accept="image/*">

          

            <!-- Submit Button -->
            <button type="submit" class="submit-button">Add Room</button>
        </form>
    </div>
</div>
</body>

</html>