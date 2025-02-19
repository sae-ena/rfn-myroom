<?php 
require("http://roomfinder.whf.bz/admin/dbConnect.php");

$result =$conn->query('SELECT * from users');

var_dump($result);


 if (isset($_FILES['room_image']) && count($_FILES['room_image']['name']) > 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        // Loop through each file
        foreach ($_FILES['room_image']['name'] as $index => $file_name) {
            $file_tmp = $_FILES['room_image']['tmp_name'][$index];
            $file_size = $_FILES['room_image']['size'][$index];
            $file_error = $_FILES['room_image']['error'][$index];

            // Validate file size
            if ($file_size > $max_file_size) {
                $validationError = "Error: File size exceeds the maximum limit of 2 MB.";
                break;
            }

            // Validate file type
            if (!in_array(mime_content_type($file_tmp), $allowed_types)) {
                $validationError = "Error: Only JPEG, PNG, and GIF files are allowed.";
                break;
            }

            // Generate unique file name and set upload path
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create the uploads directory if it doesn't exist
            }

            // Generate a unique filename to avoid overwriting
            $new_file_name = uniqid() . '_' . basename($file_name);
            $upload_path = $upload_dir . $new_file_name;

            // Move the uploaded file to the desired directory
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $validationError = "Error: Failed to upload the image.";
                break;
            }

            // You can insert the file info into the database if needed.
            $currentDateTime = date('Y-m-d H:i:s');
            $query = "INSERT INTO media (image_name, image_path, status, created_at) VALUES (?, ?, ?, ?);";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("ssis", $file_name, $upload_path, $roomStatus, $currentDateTime);
                if (!$stmt->execute()) {
                    $formError = "Error saving media.";
                    break;
                }
            }
        }
    }