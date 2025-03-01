<?php

require '../admin/dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $formId = $_POST['formId'];
    $formName = $_POST['formName'];
    $formSlug = $_POST['formSlug'];
    $description = $_POST['description'];
    $status = $_POST['status'] ==="active" ? 1 : 0 ;
    $formBgClr = $_POST['background_color'] ;
    $formBgImg= $_POST['background_image'] ;
    $fields = $_POST['fields'];

    // Check if the form slug is unique
   
    $slugQuery = "SELECT * FROM form_managers WHERE form_slug = '$formSlug' AND form_id != '$formId'";
    $slugResult = mysqli_query($conn, $slugQuery);

    if ($slugResult && mysqli_num_rows($slugResult) > 0) {
        if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
            exit("Error: Form slug must be unique.");
        } 
        echo "Error: Form slug must be unique.";
        exit;
    }

    // Format the fields properly
    $formattedFields = [];
    foreach ($fields as $field) {
        $fieldData = [
            "label" => $field["label"],
            "type" => $field["type"],
            "name" => $field["name"],
            "placeholder" => $field["placeholder"] ?? "",
            "required" => isset($field["required"])
        ];
        
        // If it's a select field, store options as an array
        if ($field["type"] == "select" && !empty($field["options"])) {
            $fieldData["options"] = array_map('trim', explode(',', $field["options"]));
        }

        $formattedFields[] = $fieldData;
    }

    // Save to JSON file
    $jsonFieldData = json_encode($formattedFields, JSON_PRETTY_PRINT);

    // Check if the form exists in the database
    $query = "SELECT * FROM form_managers WHERE form_id = '$formId'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Update existing form
        $updateQuery = "UPDATE form_managers SET form_name = ?, form_slug = ? ,description = ?, status = ?, field_detail = ? , updated_at = ? ,background_image = ? , background_color = ? WHERE form_id = ?";
        $currentDateTime = date("Y-m-d H:i:s"); // Current date and time in format: YYYY-MM-DD HH:MM:SS
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssisssss", $formName,$formSlug, $description, $status, $jsonFieldData,$currentDateTime,$formBgImg,$formBgClr,$formId);
        
        if ($stmt->execute()) {
            echo "Form updated successfully!";
            if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
                exit("Success: Form updated Successfully");
            } 
        } else {
            echo "Error updating form: " . $stmt->error;
            if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
                exit("Error: While updating form: " . $stmt->error);
            } 
        }
    } else {
        
        // Insert new form
        $insertQuery = "INSERT INTO form_managers (form_id, form_name, form_slug ,description, status, field_detail,background_image,background_color ,created_at) VALUES (?, ?, ?,?, ?, ?,?,?,?)";
        
        $currentDateTime = date("Y-m-d H:i:s"); // Current date and time in format: YYYY-MM-DD HH:MM:SS
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssissss", $formId, $formName, $formSlug , $description, $status, $jsonFieldData,$formBgImg, $formBgClr , $currentDateTime);
        
        if ($stmt->execute()) {
            echo "Form saved successfully!";
            if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
                exit("Success: Form saved successfully! ");
            } 
        } else {
            echo "Error saving form: " . $stmt->error;
            if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
                exit("Error: While saving form: " . $stmt->error);
            } 
        }
    }

    echo "<h2>Form saved successfully!</h2>";
    if( $_SERVER['HTTP_REFERER'] == "http://localhost:8000/dynaform/dynaform.php"){
        exit("Success: Form saved successfully! ");
    } 
    echo "<a href='dynaform.php'>Go Back</a>";
}
?>
