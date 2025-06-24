<?php
require "leftSidebar.php";
require('../helperFunction/helpers.php');
require "dbConnect.php";
$formTitle ="Add New Room Listing";
$buttonText = "Add Room";
$roomTitle = $roomLocation = $roomPrice = $roomStatus = $roomType = $roomDescription = $roomImage = "";$new_file_name="";
$roomId = null;
if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET['id'])) {
    $formTitle ="Edit Room Information | UID : ".$_GET['id'];
    $buttonText = "Update Room";
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
    $roomPrice = floatval($_POST['price']); 
    $roomStatus = mysqli_real_escape_string($conn, $_POST['status']);
    $roomType = mysqli_real_escape_string($conn, $_POST['type']);
    $roomDescription = mysqli_real_escape_string($conn, $_POST['description']);
    $imagePath = mysqli_real_escape_string($conn, $_POST['room_image']);
    $new_file_name = mysqli_real_escape_string($conn, $_POST['room_image']);
    if(isset($_POST['enabledEdit']))  $EditMode = mysqli_real_escape_string($conn, $_POST['enabledEdit']);

  

    if(strlen($roomTitle) > 155){
        $form_error = "Title should be less than 155 characters";
    }
    if(strlen($roomLocation) > 255){
        $form_error = "Location should be less than 255 characters";
    }
    if(strlen($roomTitle ) < 3){
        $form_error = "Title should be more than 3 characters";
    }
    if(strlen($roomLocation) < 3){
        $form_error = "Location should be more than 3 characters";
    }

    if($roomPrice <= 0 || !is_numeric($roomPrice) || strlen($roomPrice) > 10 || !preg_match("/^[0-9]*$/",$roomPrice)){
        $form_error = "Invalid Room Price";
    }
    
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
            $validationError = "Error: Only JPEG, PNG files are allowed.";
          
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
    

    
    empty($roomStatus) ? $form_error = "Status field is Required" : "";
    empty($roomType) ? $form_error = "RoomType field is Required" : "";
    empty($imagePath) ? $form_error = "Image is Required" : "";
    empty($roomType) ? $roomType = "Room Type  is Required" : "";
    empty($roomPrice) ? $form_error = "Price field is Required" : "";
    empty($roomLocation) ? $form_error = "Location  is Required" : "";
    empty($roomTitle) ? $form_error = "Title field is Required" : "";

    if (!preg_match('/[a-zA-Z]/', $roomTitle)) {
        $form_error = "Invalid Title field.";
    }
    

    if(! isset($form_error) ){
    if(is_numeric($EditMode)){

    if (! isset($form_error)) {
        // $imagePathPrevious = convertToNullIfEmpty($_POST['previousImagePath']);
        // $imagePath = $imagePathPrevious ?? $new_file_name;
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
}

// Email Template CMS Section
if (isset($_GET['section']) && $_GET['section'] === 'email_templates') {
    require('dbConnect.php');
    // Handle CRUD actions here (scaffold only)
    ?>
    <h2>Email Template Management</h2>
    <form id="email-template-form" method="POST" action="">
        <label>Subject Title:<br><input type="text" name="subject_title" required value="OTP Verification Code"></label><br><br>
        <label>User Message:<br><textarea id="user_message" name="user_message" rows="6">Hello {{name}},<br><br>Your OTP code is <b>{{otp}}</b>. It will expire in {{expires}} minutes.<br><br>If you did not request this, please ignore this email.</textarea></label><br><br>
        <label>Admin Mail:<br><input type="email" name="admin_mail" value=""></label><br><br>
        <label>Admin Message:<br><textarea id="admin_message" name="admin_message" rows="6"></textarea></label><br><br>
        <label>Status:<br>
            <select name="status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </label><br><br>
        <label>Template Variables (comma separated):<br><input type="text" name="template_variables" value="name,otp,expires" placeholder="e.g. name,email,otp"></label><br><br>
        <div style="color:#888;font-size:0.95em;margin-bottom:1em;">Note: Use <b>{{name}}</b>, <b>{{otp}}</b>, and <b>{{expires}}</b> in your message. These will be replaced with actual values when sending the email.</div>
        <button type="submit">Save Template</button>
    </form>
    <hr>
    <h3>Existing Email Templates</h3>
    <table border="1" cellpadding="6" style="width:100%;max-width:900px;">
        <thead><tr><th>ID</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <!-- List templates here (scaffold) -->
        </tbody>
    </table>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({ selector:'#user_message', menubar:false, plugins:'code', toolbar:'undo redo | bold italic underline | code', height:200 });
      tinymce.init({ selector:'#admin_message', menubar:false, plugins:'code', toolbar:'undo redo | bold italic underline | code', height:200 });
    </script>
    <?php
    exit;
}

?>
<?php if (isset($validationError)): ?>
    <div class="danger-notify">
        <span id="errorMessage"><?php echo $validationError; ?></span>
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
            <label for="room-title">Room Title: <span class="requiredRoomForm">*</span></label>
            <input type="text" id="room-title" name="title" placeholder="Enter room title"
                value="<?= htmlspecialchars($roomTitle) ?>">

            <!-- Location -->
            <label for="location">Location:<span class="requiredRoomForm">*</span></label>
            <input type="text" id="street-address" name="location" placeholder="Street Address"
                value="<?= htmlspecialchars($roomLocation) ?>">

            <!-- Price -->
            <label for="price">Price:<span class="requiredRoomForm">*</span></label>
            <input type="number" id="price" name="price" placeholder="Enter price"
                value="<?= htmlspecialchars($roomPrice) ?>">

            <!-- Room Type -->
            <label for="room-type">Room Type:<span class="requiredRoomForm">*</span></label>
    <select id="room-type" name="type">
        <option value="1BHK" <?= htmlspecialchars($roomType) == "1BHK" ? 'selected' : '' ?>>1 BHK</option>
        <option value="2BHK" <?= htmlspecialchars($roomType) == "2BHK" ? 'selected' : '' ?>>2 BHK</option>
        <option value="singleRoom" <?= htmlspecialchars($roomType) == "singleRoom" ? 'selected' : '' ?>>Single Room</option>
        <option value="apartment" <?= htmlspecialchars($roomType) == "apartment" ? 'selected' : '' ?>> Apartment</option>
    </select>
            
            <!-- <input type="file" id="photos" name="room_image" accept="image/*" > -->
            <label for="selected-image">Image :<span class="requiredRoomForm">*</span></label>
            <div style="display: flex; align-items: center;">
    <?php if (!empty($roomImage)): ?>
        <img src="<?php echo $roomImage ?>" alt="room image" style="width: 90px; height: 100px; margin-left: 10px;"/>
    <?php endif; ?>
</div>
<!-- This is the text input where the selected image path will be shown -->
<input type="text" id="selectedImage" name="room_image" value="<?= htmlspecialchars($roomImage) ?>" readonly placeholder="Double click on an image to select it">
 <!-- Upload Photos Button -->
 <button type="button" class="upload-btn" onclick="showImageUploadModal()">Upload Photo</button>

            <!-- Description -->
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" placeholder="Add a description..."
                requiredRoomForm><?= htmlspecialchars($roomDescription) ?></textarea>

            <label for="room-type">Status:<span class="requiredRoomForm">*</span></label>
            <select id="room-type" name="status" required>
                <option value="" selected disabled>Select Room Status</option>
                <option value="active" <?= htmlspecialchars($roomStatus)==="active"?"selected":"" ?> >Active</option>
                <option value="inActive"  <?= htmlspecialchars($roomStatus)==="inActive"?"selected":"" ?> >InActive</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="submit-button"><?php echo $buttonText; ?></button>
        </form>
    </div>
</div>
<div id="imageUploadModal" class="modal">
    <div class="modal-content">
    <span class="close" onclick="closeImageUploadModal()">&times;</span>
    <h2>Upload Image</h2>
    <form id="imageUploadForm" method="post" enctype="multipart/form-data">
        <input type="file" id="photos" name="room_image[]" accept="image/*" multiple>
        <input type="text" name="status" value="0" hidden>
        <button type="submit" id="imageUploadBtn">Upload</button>
    </form>
</div>

<!-- Result Div -->
<div id="resultRoom">

              
                <?php
                $query = "Select * from media ORDER BY created_at desc;";
                $resultMedia = mysqli_query($conn,$query);
                if(mysqli_num_rows($resultMedia) > 0){
                    $index =0;
                    while($rows = mysqli_fetch_assoc($resultMedia)){
                        echo '<div class="mediaImg" style="padding: 10px; border: 1px solid #ddd; display: inline-block; margin: 10px;">
                          <input type="text" hidden name="image_path" id="imgPath" value ="'.$rows["image_path"].'">
                                <img src="/admin/'. $rows["image_path"] .'" alt="Image" style="width: 220px; height: 160px; object-fit: cover; border-radius: 8px;">
                              </div>';
                    }
                } else {
                    echo "<h2 style='text-align: center; color: #333;'>NO IMAGE UPLOADED</h2>";
                }
                

                ?>
                <!-- Modal structure -->


<div id="errorModal" style=" display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
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
            </p>


        <!-- Close Button -->
        <button onclick="document.getElementById('errorModal').style.display='none'" 
            style="background-color: #d32f2f; color: white; border: none; padding: 10px 20px; 
            font-size: 14px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2); transition: 0.3s;">
            Close
        </button>

    </div>
</div>
<div id="successModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
    
    <div style="position: relative; margin: 15% auto; background-color: #e8f5e9; padding: 25px; width: 350px; 
        border-radius: 10px; text-align: center; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); 
        border: 2px solid #388e3c; animation: fadeIn 0.4s ease-in-out;">
        
        <!-- Success Icon -->
        <div style="width: 60px; height: 60px; background-color: #388e3c; color: white; 
            font-size: 30px; font-weight: bold; line-height: 60px; text-align: center; 
            border-radius: 50%; margin: -50px auto 10px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);">
            ✓
        </div>

        <h3 style="color: #2e7d32; margin-bottom: 10px; font-family: Arial, sans-serif;">Success</h3>

        <hr style="border: 2px solid #388e3c; width: 100%;">

        <p id="successMessage" style="color: #333; font-size: 16px; font-family: Arial, sans-serif; 
            margin: 15px 0; padding: 10px; background: #c8e6c9; border-radius: 5px; box-shadow: inset 0px 1px 4px rgba(0,0,0,0.1);">
        </p>

        <!-- Close Button -->
        <button onclick="document.getElementById('successModal').style.display='none'" 
            style="background-color: #388e3c; color: white; border: none; padding: 10px 20px; 
            font-size: 14px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2); transition: 0.3s;">
            Close
        </button>

    </div>
</div>

                
        
            </div>
</body>

<!-- Add your custom JavaScript here -->
<script>
// Show the image upload modal
function showImageUploadModal() {
    document.getElementById("imageUploadModal").style.display = "block";
}

// Close the image upload modal
function closeImageUploadModal() {
    document.getElementById("imageUploadModal").style.display = "none";
}
window.onload = function() {
    
    if (localStorage.getItem("showModal") === "true") {
        // Show the modal automatically
        document.getElementById("imageUploadModal").style.display = "block";
        
        // Clear the flag so the modal doesn't show again after another reload
        localStorage.removeItem("showModal");
    }
};
// Close modal if clicked outside
window.onclick = function(event) {
    var modal = document.getElementById("imageUploadModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
const imgElements = document.getElementsByClassName("mediaImg");

// Loop through each element and add the event listener
Array.from(imgElements).forEach(imgDiv => {
    imgDiv.addEventListener("dblclick", (e) => {
        const imgDiv = e.currentTarget;
        
        // Access the hidden input field inside the clicked div
        const imgPathInput = imgDiv.querySelector("input[name='image_path']");
        
        // Get the value of the hidden input
        const imagePath = imgPathInput.value;
        document.getElementById("selectedImage").value=imagePath;
        var modal = document.getElementById("imageUploadModal");
   
        modal.style.display = "none";
        
    });
});
function showErrorModal() {
    // Display the modal
    document.getElementById("errorModal").style.display = "block";
}
function closeErrorModal() {
    document.getElementById("errorModal").style.display = "none";
}
function showSuccessImageUploadModal() {
    // Display the modal
    document.getElementById("successModal").style.display = "block";
    localStorage.setItem("showModal", "true");
    setTimeout(function() {
        window.location.reload();  // Reload the page
    }, 1700); 
}
document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting the traditional way

    const fileInput = document.getElementById('photos');
    const file = fileInput.files;

    const form =this;
    // Validate if a file has been selected
    if (file.length == "0") {
        return false; 
    }
    // Get the form data, including the file input
    const formData = new FormData(this);

    // Send the form data via Fetch API
    fetch('addMedia.php', {
        method: 'POST',
        body: formData  // Form data includes the file input and other form fields
    })
    .then(response => response.text())  // We expect a text response from PHP
    .then(data => {
        
        resAlreadyUploaded=data.includes("Connection failed to upload image");
if(resAlreadyUploaded){
    document.getElementById("errorMessage").textContent = "Image has been uploaded already.";
    showErrorModal();
    return;
}
        resTypeValidation=data.includes("Only JPEG, PNG files are allowed");
if(resTypeValidation){
    document.getElementById("errorMessage").textContent = "Only JPEG, PNG files are allowed";
    showErrorModal();
    return;
}
        resMaxSize=data.includes("File size exceeds the maximum limit of 2 MB");
if(resMaxSize){
    document.getElementById("errorMessage").textContent = "File size exceeds the maximum limit of 2 MB";
    showErrorModal();
    return;
}

document.getElementById("successMessage").textContent = "Image has been uploaded .";
        //window.location.href="form.php"; 
        showSuccessImageUploadModal();
        // document.getElementById('result').innerHTML = data; 
    })
    .catch(error => {
        console.error('Error:', error);
    });
});




</script>

<!-- Add some basic CSS for the modal -->
<style>
.modal {
    display: none;  /* Hidden by default */
    position: absolute;
    z-index: 1;  /* Sit on top */
    right: 140px;
    top: 20px;
    width: 70%;
    padding-top: 60px;
    color: white;
}

.modal-content {
    background-color:rgb(108, 235, 112);
    padding: 0 200px 0 100px;
    border: 31px solid rgb(106, 229, 110) ;
    border-radius: 35px 29px;
    width: 70%; /* Adjust depending on screen size */
    position: relative; /* Positioning to center it */
    top: 60px;
    left: 50%; /* Move to the middle horizontally */
    transform: translate(-50%, -50%); /* Offset by half of the element's width and height to truly center it */
    box-sizing: border-box; /* Ensures the padding doesn't affect the overall width */
}
.close {
    background-color: rgba(255, 255, 255, 0.8); 
    border-radius: 50px;/* Black w/ opacity */
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute; /* Or you could use float: right; */
    top: 10px; /* Adjust as needed */
    right: 10px; /* Align to the right */
    padding: 3px;
    cursor: pointer; /* Makes the button look clickable */
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
#resultRoom{
    position: relative;
    top: 0px;
    background-color: rgb(255, 255, 255,0.8); /* Black w/ opacity */
    border-radius: 28px;
}
.upload-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    margin: 10px;
    border: none;
    cursor: pointer;
    text-align: center;
}

.upload-btn:hover {
    background-color: #45a049;
}
</style>

</html>