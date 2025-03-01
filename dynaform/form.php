<?php

require "../admin/leftSidebar.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['formid'])) {

    require "../admin/dbConnect.php";
    $formid = $_GET['formid'];
    
    // Assuming you have a database connection $conn
    $query = "SELECT * FROM form_managers WHERE form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $formid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo ' <link rel="stylesheet" href="../admin/login.css">';
        $row = $result->fetch_assoc();
        $field_details = json_decode($row['field_detail'], true);
        
        // Display the form title
        echo '<div class="dashboard-content"  >
        <div class="form-container"
    style="margin-left: 270px; ">';

        if(isset($row['background_image']) && strlen($row['background_image']) > 6){
            echo '<div class=""
            style="
            background-image: url(\'../admin/' . $row["background_image"] . '\');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        ">';
    
    
        }else{
        echo'<div style="background-color:'.$row["background_color"].';
    display: flex; 
    align-items: center; 
    justify-content: center; 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    height: auto;">
';

        }
       
       echo '<div class="auth-container">
          
            <!-- Glassmorphism Forms -->
            <div id="login-form" class="form-card glass visible">';
            echo '<h1>' . htmlspecialchars($row['form_name']) . '</h1>';
        echo '<form action="submit_form.php" method="POST">';
        foreach ($field_details as $index => $field) {

        
           
            echo '<label for="' . htmlspecialchars($field['name']) . '">' . htmlspecialchars($field['label']) ;  if($field['required'] == true){ echo'<span class="required" style="
    color: red;
    font-size: 16px;
    margin-left: 5px;
">*</span>';};  echo'</label>';


if ($field['type'] == 'select') {
    echo '<select name="fields[' . $index . '][value]" id="field_' . $index . '" ' . ($field['required'] ? 'required' : '') . '>';
    foreach ($field['options'] as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
} else {
    echo '<input type="' . htmlspecialchars($field['type']) . '" 
           name="fields[' . $index . '][value]" 
           id="field_' . $index . '" 
           placeholder="' . htmlspecialchars($field['placeholder']) . '" 
           ' . ($field['required'] ? 'required' : '') . '>';
}

            
        }
        echo '<button type="submit" name="register" class="submit-button">Submit</button>';
        echo '</form>
        </div>
        </div>
        </div>
        </div>
        </div>
        ';
        
    } else {
        echo "Invalid formid";
        return;
    }
} else {
    header('Location: dynaform.php');
    exit();
}

?>

