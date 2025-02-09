<?php
require "leftSidebar.php";
require('../helperFunction/helpers.php');
require "dbConnect.php";

// Initialize variables
$formTitle = "Add New Media";
$roomStatus = $roomImage = "";
$new_file_name = $roomId = null;

if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET['id'])) {
    $formTitle = "Edit Media Information | UID : " . $_GET['id'];
    $roomId = $_GET['id'];

    // Fetch room data if ID is provided
    if ($stmt = $conn->prepare("SELECT * FROM media WHERE room_id = ?")) {
        $stmt->bind_param("i", $roomId);  // 'i' means integer type
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $roomData = $result->fetch_assoc();
            $roomStatus = (int)$roomData['room_status'];
            $roomImage = $roomData['room_image'];
        } else {
            header("Location: /admin/form.php");
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomStatus = (int)mysqli_real_escape_string($conn, $_POST['status']);
    $EditMode = isset($_POST['enabledEdit']) ? mysqli_real_escape_string($conn, $_POST['enabledEdit']) : null;

    // Handle file upload
    if (isset($_FILES['room_image']['name']) && $_FILES['room_image']['size'] > 0) {
        $file_name = $_FILES['room_image']['name'];
        $file_tmp = $_FILES['room_image']['tmp_name'];
        $file_size = $_FILES['room_image']['size'];
        $file_error = $_FILES['room_image']['error'];

        // Define allowed file types and size limit (2MB)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2 * 1024 * 1024;  // 2 MB

        // Validate file size
        if ($file_size > $max_file_size) {
            $validationError = "Error: File size exceeds the maximum limit of 2 MB.";
        }

        // Validate file type
        if (!in_array(mime_content_type($file_tmp), $allowed_types)) {
            $validationError = "Error: Only JPEG, PNG, and GIF files are allowed.";
        }

        // Generate unique file name and set upload path
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Create the uploads directory if it doesn't exist
        }

        // Generate a unique filename to avoid overwriting
        $new_file_name = uniqid() . '_' . basename($file_name);
        $upload_path = $upload_dir . $new_file_name;

        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            $validationError = "Error: Failed to upload the image.";
        }
    }

    // Process data for insert or update
    if (isset($EditMode) && is_numeric($EditMode)) {
        if (!isset($validationError)) {
            // Handle update
            $imagePathPrevious = convertToNullIfEmpty($_POST['previousImagePath']);
            $imagePath = $imagePathPrevious ?? $new_file_name;

            $query = "UPDATE media SET image_name = ?, image_path = ?, status = ? WHERE id = ?;";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("ssii", $file_name, $upload_path, $roomStatus, $EditMode);
                if ($stmt->execute()) {
                    $successfullyRoomAdded = "Media has been updated.";
                    $roomStatus = $roomImage = "";
                } else {
                    $formError = "Error updating media.";
                }
            }
        }
    } else {
        // Handle insert
        $currentDateTime = date('Y-m-d ');
        $query = "INSERT INTO media (image_name, image_path, status, created_at) VALUES (?, ?, ?, ?);";

        if (!isset($validationError)) {
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("ssis", $file_name, $upload_path, $roomStatus, $currentDateTime);
                if ($stmt->execute()) {
                    $successfullyRoomAdded = "Media has been saved.";
                    $roomStatus = $roomImage = "";
                } else {
                    $formError = "Error saving media.";
                }
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

    <div class="form-container" style="margin-left: 260px; padding: 5px; display: flex; justify-content: center; align-items: center;">
        <form id="media-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <h1><?php echo $formTitle; ?></h1>
            <input type="number" hidden value="<?= htmlspecialchars($roomId) ?>" name="enabledEdit">

            <!-- Upload Photos -->
            <label for="photos">Upload Photos:</label>
            <?php if (isset($roomImage)): ?>
                <img src="/admin/uploads/<?= htmlspecialchars($roomImage) ?>" alt="" style="width: 100px; height: auto;">
                <input type="text" hidden name="previousImagePath" value="<?= htmlspecialchars($roomImage) ?>">
            <?php endif; ?>
            <input type="file" id="photos" name="room_image" accept="image/*">

            <label for="room-status">Status:</label>
            <select id="room-status" name="status" required>
                <option value="" selected disabled>Select Media Status</option>
                <option value="1" <?= htmlspecialchars($roomStatus) === 1 ? "selected" : "" ?>>Active</option>
                <option value="0" <?= htmlspecialchars($roomStatus) === 0 ? "selected" : "" ?>>InActive</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="submit-button"><?= $formTitle === "Add New Media" ? "Add Media" : "Update Media" ?></button>
        </form>
    </div>
</div>

</body>
</html>
