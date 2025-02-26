<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = $_POST['fields'];

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
    file_put_contents("form_data.json", json_encode($formattedFields, JSON_PRETTY_PRINT));

    echo "<h2>Form saved successfully!</h2>";
    echo "<a href='dynaform.php'>Go Back</a>";
}
?>
