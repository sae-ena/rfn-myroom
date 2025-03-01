<?php
// Including the database configuration file
require_once '../admin/dbConnect.php';

// Fetching the field types from the database
$query = "SELECT * FROM form_fields";
$result = $conn->query($query);

$field_types = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $field_types[] = $row['field_name']; // Adding field types to the array
    
    }
}
?>

<script>
// PHP array converted to JavaScript
let fieldTypes = <?php echo json_encode($field_types); ?>;

let fieldHtml = `
    <div class="field" id="field_${index}" draggable="true" class="draggable">
        <label>Label: <input type="text" name="fields[${index}][label]" required></label>
        <label>Type: 
            <select name="fields[${index}][type]" onchange="toggleOptions(${index})">
`;

fieldTypes.forEach(type => {
    fieldHtml += `<option value="${type}">${type.charAt(0).toUpperCase() + type.slice(1)}</option>`;
});

fieldHtml += `
            </select>
        </label>
        <label>Name: <input type="text" name="fields[${index}][name]" required></label>
        <label>Placeholder: <input type="text" name="fields[${index}][placeholder]"></label>
        <label>Required: <input type="checkbox" name="fields[${index}][required]" value="true"></label>
        
        <div id="options_${index}" class="options-container" style="display:none;">
            <label>Options (comma-separated): <input type="text" name="fields[${index}][options]"></label>
        </div>

        <button type="button" onclick="removeField(${index})">Remove</button>
        <hr>
    </div>`;

document.body.innerHTML += fieldHtml;
</script>
