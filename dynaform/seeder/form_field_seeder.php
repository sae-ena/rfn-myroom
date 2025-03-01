<?php
require_once '../../admin/dbConnect.php';  // Include the database connection file

// Array of input types to insert into the database
$field_types = [
    'text', 'password', 'email', 'url', 'tel', 'number', 'range', 'date', 
    'time', 'datetime-local', 'month', 'week', 'checkbox', 'radio', 'file', 
    'hidden', 'button', 'submit', 'reset', 'image', 'search', 'color'
];

// Array of titles corresponding to field types
$field_titles = [
    'Text ', 'Password ', 'Email ', 'URL ', 'Telephone ', 
    'Number ', 'Range ', 'Date Picker', 'Time Picker', 'DateTime Picker',
    'Month Picker', 'Week Picker', 'Checkbox', 'Radio Button', 'File Upload', 
    'Hidden ', 'Button', 'Submit Button', 'Reset Button', 'Image Upload',
    'Search ', 'Color Picker'
];

// Function to check if field name already exists in the database
function fieldExists($conn, $field_name) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM form_feilds WHERE field_name = ?");
    $stmt->bind_param("s", $field_name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    return $count > 0;
}

// Loop through the field types and insert each one if it doesn't already exist
foreach ($field_types as $index => $field_type) {
    // Get corresponding field title
    $field_title = $field_titles[$index];

    if (fieldExists($conn, $field_type)) {
        echo "Field '$field_type' already exists. Skipping insertion.<br>" ."\n";
    } else {
        // Insert the field name and field title if it doesn't already exist
        $stmt = $conn->prepare("INSERT INTO form_feilds (field_name, field_title) VALUES (?, ?)");
        $stmt->bind_param("ss", $field_type, $field_title);  // Bind both $field_type and $field_title as strings
        
        if ($stmt->execute()) {
            echo "Field '$field_type' with title '$field_title' inserted successfully.<br>" ."\n";
        } else {
            echo "Error inserting '$field_type': " . $stmt->error . "<br>";
        }

        $stmt->close();  // Close the statement
    }
}

// Close the database connection
$conn->close();
?>
