<?php
// Read existing JSON data
$jsonFile = 'form_data.json';
$formData = json_decode(file_get_contents($jsonFile), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Form</title>
    <script>
        function addField() {
            const container = document.getElementById("fieldsContainer");
            const index = container.children.length;
            
            let fieldHtml = `
                <div class="field" id="field_${index}">
                    <label>Label: <input type="text" name="fields[${index}][label]" required></label>
                    <label>Type: 
                        <select name="fields[${index}][type]" onchange="toggleOptions(${index})">
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="password">Password</option>
                            <option value="select">Select</option>
                        </select>
                    </label>
                    <label>Name: <input type="text" name="fields[${index}][name]" required></label>
                    <label>Placeholder: <input type="text" name="fields[${index}][placeholder]"></label>
                    <label>Required: <input type="checkbox" name="fields[${index}][required]" value="true"></label>
                    <div id="options_${index}" style="display:none;">
                        <label>Options (comma-separated): <input type="text" name="fields[${index}][options]"></label>
                    </div>
                    <button type="button" onclick="removeField(${index})">Remove</button>
                    <hr>
                </div>`;
            
            container.insertAdjacentHTML("beforeend", fieldHtml);
        }

        function removeField(index) {
            document.getElementById(`field_${index}`).remove();
        }

        function toggleOptions(index) {
            const type = document.querySelector(`select[name="fields[${index}][type]"]`).value;
            document.getElementById(`options_${index}`).style.display = type === "select" ? "block" : "none";
        }
    </script>
</head>
<body>

    <h2>Manage Form Fields</h2>
    
    <form action="save_form.php" method="POST">
        <div id="fieldsContainer">
            <?php foreach ($formData as $index => $field): ?>
                <div class="field" id="field_<?php echo $index; ?>">
                    <label>Label: <input type="text" name="fields[<?php echo $index; ?>][label]" value="<?php echo $field['label']; ?>" required></label>
                    <label>Type: 
                        <select name="fields[<?php echo $index; ?>][type]" onchange="toggleOptions(<?php echo $index; ?>)">
                            <option value="text" <?php echo $field['type'] == 'text' ? 'selected' : ''; ?>>Text</option>
                            <option value="email" <?php echo $field['type'] == 'email' ? 'selected' : ''; ?>>Email</option>
                            <option value="password" <?php echo $field['type'] == 'password' ? 'selected' : ''; ?>>Password</option>
                            <option value="select" <?php echo $field['type'] == 'select' ? 'selected' : ''; ?>>Select</option>
                        </select>
                    </label>
                    <label>Name: <input type="text" name="fields[<?php echo $index; ?>][name]" value="<?php echo $field['name']; ?>" required></label>
                    <label>Placeholder: <input type="text" name="fields[<?php echo $index; ?>][placeholder]" value="<?php echo $field['placeholder'] ?? ''; ?>"></label>
                    <label>Required: <input type="checkbox" name="fields[<?php echo $index; ?>][required]" value="true" <?php echo isset($field['required']) ? 'checked' : ''; ?>></label>
                    <div id="options_<?php echo $index; ?>" style="display: <?php echo $field['type'] == 'select' ? 'block' : 'none'; ?>">
                        <label>Options (comma-separated): <input type="text" name="fields[<?php echo $index; ?>][options]" value="<?php echo isset($field['options']) ? implode(',', $field['options']) : ''; ?>"></label>
                    </div>
                    <button type="button" onclick="removeField(<?php echo $index; ?>)">Remove</button>
                    <hr>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" onclick="addField()">Add Field</button>
        <button type="submit">Save Form</button>
    </form>

</body>
</html>
