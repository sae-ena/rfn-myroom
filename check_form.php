<?php
require "admin/dbConnect.php";

// Fetch the backend-setting form definition
$formQuery = "SELECT * FROM form_managers WHERE form_slug = ? AND status = 1 LIMIT 1";
$formSlug = "backend-setting";
$FormSmt = $conn->prepare($formQuery);
$FormSmt->bind_param("s", $formSlug);
$FormSmt->execute();
$formManagerResult = $FormSmt->get_result();

if ($formManagerResult && $formManagerResult->num_rows > 0) {
    $formData = $formManagerResult->fetch_assoc();
    $fields = json_decode($formData['field_detail'], true);
    
    echo "Form Name: " . $formData['form_name'] . "\n";
    echo "Form Slug: " . $formData['form_slug'] . "\n";
    echo "Fields:\n";
    
    foreach ($fields as $field) {
        echo "- Name: " . $field['name'] . ", Type: " . $field['type'] . ", Label: " . $field['label'] . "\n";
    }
} else {
    echo "Form not found!\n";
}

$conn->close();
?> 