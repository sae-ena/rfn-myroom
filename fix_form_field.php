<?php
require "admin/dbConnect.php";

// Fetch the current form definition
$formQuery = "SELECT * FROM form_managers WHERE form_slug = ? AND status = 1 LIMIT 1";
$formSlug = "backend-setting";
$FormSmt = $conn->prepare($formQuery);
$FormSmt->bind_param("s", $formSlug);
$FormSmt->execute();
$formManagerResult = $FormSmt->get_result();

if ($formManagerResult && $formManagerResult->num_rows > 0) {
    $formData = $formManagerResult->fetch_assoc();
    $fields = json_decode($formData['field_detail'], true);
    
    echo "Before fix:\n";
    foreach ($fields as $field) {
        echo "- Name: '" . $field['name'] . "', Type: " . $field['type'] . ", Label: " . $field['label'] . "\n";
    }
    
    // Fix the field name by removing trailing space
    foreach ($fields as &$field) {
        if ($field['name'] === 'table-data-text-color ') {
            $field['name'] = 'table-data-text-color';
            echo "\nFixed field name from 'table-data-text-color ' to 'table-data-text-color'\n";
        }
    }
    
    // Update the form in database
    $updatedFieldDetail = json_encode($fields, JSON_PRETTY_PRINT);
    $updateQuery = "UPDATE form_managers SET field_detail = ? WHERE form_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $updatedFieldDetail, $formData['form_id']);
    
    if ($updateStmt->execute()) {
        echo "Form updated successfully!\n";
        
        echo "\nAfter fix:\n";
        foreach ($fields as $field) {
            echo "- Name: '" . $field['name'] . "', Type: " . $field['type'] . ", Label: " . $field['label'] . "\n";
        }
    } else {
        echo "Error updating form: " . $updateStmt->error . "\n";
    }
    
    $updateStmt->close();
} else {
    echo "Form not found!\n";
}

$conn->close();
?> 