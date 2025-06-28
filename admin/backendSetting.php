<?php
require "dbConnect.php";
session_start();
require_once "leftSidebar.php";

// Fetch the backend-setting form definition (fetch once)
$formQuery = "SELECT * FROM form_managers WHERE form_slug = ? AND status = 1 LIMIT 1";
$formSlug = "backend-setting";
$FormSmt = $conn->prepare($formQuery);
$FormSmt->bind_param("s", $formSlug);
$FormSmt->execute();
$formManagerResult = $FormSmt->get_result();
$formData = null;
$fields = [];
if ($formManagerResult && $formManagerResult->num_rows > 0) {
    $formData = $formManagerResult->fetch_assoc();
    $fields = json_decode($formData['field_detail'], true);
}

$form_error = null;
$form_success = null;

// Fetch all current backend settings for pre-filling
$existingSettings = [];
$settingsResult = $conn->query("SELECT name, value FROM backend_settings WHERE status = 1");
if ($settingsResult && $settingsResult->num_rows > 0) {
    while ($row = $settingsResult->fetch_assoc()) {
        $existingSettings[$row['name']] = $row['value'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $values = [];
    // Build values array, handling checkboxes
    foreach ($fields as $field) {
        $name = $field['name'];
        if ($field['type'] === 'checkbox') {
            $values[$name] = isset($_POST[$name]) ? '1' : '0';
        } else {
            $values[$name] = isset($_POST[$name]) ? trim($_POST[$name]) : '';
        }
    }
    // Delete all existing settings
    $conn->query("DELETE FROM backend_settings");
    // Insert new settings
    $stmt = $conn->prepare("INSERT INTO backend_settings (name, value, status) VALUES (?, ?, 1)");
    foreach ($values as $name => $value) {
        $stmt->bind_param("ss", $name, $value);
        $stmt->execute();
    }
    $stmt->close();
    $form_success = "Backend settings saved successfully!";
    // Update $existingSettings for pre-fill after save
    $existingSettings = $values;
}
?>
<div class="dashboard-content">
    <h1 class="roomH1" style="color:white">Backend Setting</h1>
    <?php if (isset($form_error) && $form_error): ?>
        <div class="danger-notify"><?php echo $form_error; ?></div>
    <?php endif; ?>
    <?php if (isset($form_success) && $form_success): ?>
        <div class="success-notify"><?php echo $form_success; ?></div>
    <?php endif; ?>
    <?php
    if ($formData && is_array($fields)) {
        echo '<form method="POST" action="backendSetting.php" class="backend-setting-form" style="max-width:600px;background:#fff;padding:32px 28px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">';
        echo '<h2 style="color:#222;margin-bottom:18px;">' . htmlspecialchars($formData['form_name']) . '</h2>';
        foreach ($fields as $index => $field) {
            $name = $field['name'];
            $label = $field['label'];
            $type = $field['type'];
            $required = isset($field['required']) && $field['required'];
            $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
            // Pre-fill value: POST > DB > ''
            $value = isset($_POST[$name]) ? htmlspecialchars($_POST[$name]) : (isset($existingSettings[$name]) ? htmlspecialchars($existingSettings[$name]) : '');
            if ($type === 'select') {
                echo '<label for="' . htmlspecialchars($name) . '" style="font-weight:600;">' . htmlspecialchars($label);
                if ($required) echo '<span style="color:red;margin-left:5px;">*</span>';
                echo '</label>';
                echo '<select name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" ' . ($required ? 'required' : '') . ' class="input-field">';
                foreach ($field['options'] as $option) {
                    $selected = ($value === $option) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
                }
                echo '</select>';
                echo '<br>';
            } else if ($type === 'checkbox') {
                $checked = ($value === '1' || $value === 1) ? 'checked' : '';
                echo '<div style="display:flex;align-items:center;margin-bottom:18px;">';
                echo '<input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="1" ' . $checked . ' ' . ($required ? 'required' : '') . ' style="margin-right:8px;">';
                echo '<label for="' . htmlspecialchars($name) . '" style="font-weight:600;margin:0;">' . htmlspecialchars($label);
                if ($required) echo '<span style="color:red;margin-left:5px;">*</span>';
                echo '</label>';
                echo '</div>';
            } else {
                echo '<label for="' . htmlspecialchars($name) . '" style="font-weight:600;">' . htmlspecialchars($label);
                if ($required) echo '<span style="color:red;margin-left:5px;">*</span>';
                echo '</label>';
                echo '<input type="' . htmlspecialchars($type) . '" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="' . $value . '" placeholder="' . htmlspecialchars($placeholder) . '" ' . ($required ? 'required' : '') . ' class="input-field" style="margin-bottom:18px;">';
                echo '<br>';
            }
        }
        echo '<button type="submit" name="submit" class="edit-button modern-btn" style="margin-top:18px;">Save Settings</button>';
        echo '</form>';
    } else {
        echo '<div class="danger-notify" style="margin-top:32px;">Backend Setting form not found. Please contact the administrator.</div>';
    }
    ?>
</div> 