<?php
require "leftSidebar.php";
require('../helperFunction/helpers.php');
require "dbConnect.php";
$formTitle ="Add New Room Listing";
$roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription = $roomImage = "";$new_file_name="";
$roomId = null;
if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET['id'])) {
    $formTitle ="Edit Room Information | UID : ".$_GET['id'];
    $roomId = $_GET['id'];
    // Assuming $conn is your database connection
    if ($stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?")) {
        $stmt->bind_param("i", $roomId);  // 'i' means integer type
        $stmt->execute();
        $result = $stmt->get_result();
        if(!$result->num_rows == 1) header("Location:/admin/form.php");
        $roomData = $result->fetch_assoc();

        $roomTitle = $roomData['room_name'];
        $roomLocation = $roomData['room_location'];
        $roomPrice = $roomData['room_price'];
        $roomStatus = $roomData['room_status'];
        $roomType = $roomData['room_type'];
        $roomDescription = $roomData['room_description'];
        $roomImage = $roomData['room_image'];

    } 
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input data using mysqli_real_escape_string (optional if using prepared statements)
    $roomTitle = mysqli_real_escape_string($conn, $_POST['title']);
    $roomLocation = mysqli_real_escape_string($conn, $_POST['location']);
    $roomPrice = floatval($_POST['price']); // Ensuring it's a number
    $roomStatus = mysqli_real_escape_string($conn, $_POST['status']);
    $roomType = mysqli_real_escape_string($conn, $_POST['type']);
    $roomDescription = mysqli_real_escape_string($conn, $_POST['description']);
    if(isset($_POST['enabledEdit']))  $EditMode = mysqli_real_escape_string($conn, $_POST['enabledEdit']);
    
    if (isset($_FILES['room_image']['name']) && $_FILES['room_image']['size'] > 0 ){


        // Get the file details
        $file_name = $_FILES['room_image']['name'];
        $file_tmp = $_FILES['room_image']['tmp_name'];
        $file_size = $_FILES['room_image']['size'];
        $file_error = $_FILES['room_image']['error'];
    
        // Define allowed file types and size limit (e.g., 2MB)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2 * 1024 * 1024;  // 2 MB
    
    
        // Validate file size
        if ($file_size > $max_file_size) {
            $validationError =  "Error: File size exceeds the maximum limit of 5 MB.";
        
        }
    
        // Validate file type
        if (!in_array(mime_content_type($file_tmp), $allowed_types)) {
            $validationError = "Error: Only JPEG, PNG, and GIF files are allowed.";
          
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
    

    // $roomTitle = $_POST['title'] ?? '';   // Default to empty if not set
    // $roomLocation = $_POST['location'] ?? '';
    // $roomPrice = $_POST['price'] ?? '';
    // $roomStatus = $_POST['status'] ?? '';
    // $roomType = $_POST['type'] ?? '';
    // $roomDescription = $_POST['description'] ?? '';
    // $roomImage = $_POST['photo'] ?? '';
    
    empty($roomTitle) ? $form_error = "Title field is Required" : "";
    empty($roomPrice) ? $form_error = "Price field is Required" : "";
    empty($roomType) ? $form_error = "RoomType field is Required" : "";

    if(is_numeric($EditMode)){

    if (! isset($form_error)) {
        $imagePathPrevious = convertToNullIfEmpty($_POST['previousImagePath']);
        $imagePath = $imagePathPrevious ?? $new_file_name;
        $query = "UPDATE rooms SET room_name = ?, room_location = ?, room_price = ?, room_type = ?, room_status = ?, room_description = ?, room_image = ? WHERE room_id = ?;";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ssdssssi", $roomTitle, $roomLocation, $roomPrice, $roomType, $roomStatus, $roomDescription, $imagePath, $EditMode);
            if ($stmt->execute()) {
                $successfullyRoomAdded = "Rooms information has been updated.";
                $roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription ="";
            } else {
                $form_error = "Error updating room.";
            }
        }
           
    
    }
    }
    else{
        $currentDateTime = date('Y-m-d H:i:s');
    // Prepare the SQL query with placeholders
    $query = "INSERT INTO rooms (room_name, room_location, room_price, room_type, room_status, room_description, room_image,created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

    if (!isset($form_error)) {

        if ($stmt = $conn->prepare($query)) {

            // Bind the variables to the prepared statement
            $stmt->bind_param("ssdsssss", $roomTitle, $roomLocation, $roomPrice, $roomType, $roomStatus, $roomDescription, $new_file_name, $currentDateTime);

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
}


?>
<?php if (isset($validationError)): ?>
    <div class="danger-notify">
        <span><?php echo $validationError; ?></span>
    </div>
<?php endif; ?>


<div class="dashboard-content">
<?php require('../helperFunction/SweetAlert.php'); ?>   
<div class="form-container"
        style="margin-left: 260px; padding: 5px; display: flex; justify-content: center; align-items: center;">
        <form id="room-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <h1><?php echo $formTitle ;?></h1>
            <input type="number" hidden  value="<?= htmlspecialchars($roomId) ?>" name="enabledEdit">
            <!-- Room Title -->
            <label for="room-title">Room Title:</label>
            <input type="text" id="room-title" name="title" placeholder="Enter room title"
                value="<?= htmlspecialchars($roomTitle) ?>">

            <!-- Location -->
            <label for="location">Location:</label>
            <input type="text" id="street-address" name="location" placeholder="Street Address"
                value="<?= htmlspecialchars($roomLocation) ?>">

            <!-- Price -->
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" placeholder="Enter price"
                value="<?= htmlspecialchars($roomPrice) ?>">

            <!-- Room Type -->
            <label for="room-type">Room Type:</label>
    <select id="room-type" name="type">
        <option value="1BHK" <?= htmlspecialchars($roomType) == "1BHK" ? 'selected' : '' ?>>1 BHK</option>
        <option value="2BHK" <?= htmlspecialchars($roomType) == "2BHK" ? 'selected' : '' ?>>2 BHK</option>
        <option value="Single Room" <?= htmlspecialchars($roomType) == "Single Room" ? 'selected' : '' ?>>Single Room</option>
        <option value="Studio" <?= htmlspecialchars($roomType) == "Studio" ? 'selected' : '' ?>>Studio</option>
        <option value="Shared Room" <?= htmlspecialchars($roomType) == "Shared Room" ? 'selected' : '' ?>>Shared Room</option>
        <option value="Entire Apartment" <?= htmlspecialchars($roomType) == "Entire Apartment" ? 'selected' : '' ?>>Entire Apartment</option>
    </select>


            <!-- Upload Photos -->
            <label for="photos">Upload Photos:</label>
            <?php if(isset($roomImage)){
                 echo'  <img src="/admin/uploads/'.$roomImage.'" alt="" style="width: 100px; height: auto;">
                 <input type="text" hidden name="previousImagePath" value="'.$roomImage.'" >
             ';
            }?>
            <input type="file" id="photos" name="room_image" accept="image/*" >

            <!-- Description -->
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" placeholder="Add a description..."
                required><?= htmlspecialchars($roomDescription) ?></textarea>

            <label for="room-type">Status:</label>
            <select id="room-type" name="status" required>
                <option value="" selected disabled>Select Room Status</option>
                <option value="active" <?= htmlspecialchars($roomStatus)==="active"?"selected":"" ?> >Active</option>
                <option value="inActive"  <?= htmlspecialchars($roomStatus)==="inActive"?"selected":"" ?> >InActive</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="submit-button">Add Room</button>
        </form>
    </div>
</div>
</body>

</html>