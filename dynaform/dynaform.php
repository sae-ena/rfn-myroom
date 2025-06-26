<?php
require "../admin/leftSidebar.php";
include '../admin/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['formId'])) {
    $formId = $_GET['formId'];
    $query = "SELECT * FROM form_managers WHERE form_id = '$formId'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    if(isset($row)){

        $formData = $row ? json_decode($row['field_detail'], true) : [];
        $formName = $row['form_name'];
        $formSlug = $row['form_slug'];
        $formDescription = $row['description'];
        $formStatus = $row['status'];
        $formBgImg = $row['background_image'] ?? null ;
        $formBgClr = $row['background_color'] ?? null;

        $typeQuery = "Select * from form_fields where field_status = 1";
        $typeResult = $conn->query($typeQuery);
        $typeArray = [];
        if ($typeResult && $typeResult->num_rows > 0) {
            while ($typeRow = mysqli_fetch_assoc($typeResult)) {
               $typeArray[] = $typeRow;
            }
        }
       
    }
    else{
        header("Location:".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']);
    }
} else {
    $formData = [];
    $formId = uniqid();
}
?>

    <style>
        h2 { text-align: center; color: #444; }
        .field { background: #fafafa; padding: 15px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; position: relative; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        .delete-button { background: #f44336; position: absolute; top: 5px; right: 5px; padding: 5px 10px; font-size: 12px; }
        .delete-button:hover { background: #e53935; }
        .add-field-button { background-color: #2196F3; width: 100%; margin-top: 15px; }
        .save-button { width: 100%; background: #FF9800; margin-top: 10px; }
        .sort-buttons { display: flex; gap: 5px; margin-top: 10px; }
        .sort-buttons button { padding: 6px 10px; font-size: 12px; }
        .move-up { background: #4CAF50; }
        .move-down { background: #ff9800; }

        .required-field {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        .required-field input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .modal {
    display: none;  /* Hidden by default */
    position: absolute;
    z-index: 1;  /* Sit on top */
    right: 140px;
    top: 20px;
    width: 70%;
    padding-top: 60px;
    color: white;
}

.modal-content {
    background-color:rgb(108, 235, 112);
    padding: 0 200px 0 100px;
    border: 31px solid rgb(106, 229, 110) ;
    border-radius: 35px 29px;
    width: 70%; /* Adjust depending on screen size */
    position: relative; /* Positioning to center it */
    top: 60px;
    left: 50%; /* Move to the middle horizontally */
    transform: translate(-50%, -50%); /* Offset by half of the element’s width and height to truly center it */
    box-sizing: border-box; /* Ensures the padding doesn’t affect the overall width */
}
.close {
    background-color: rgba(255, 255, 255, 0.8); 
    border-radius: 50px;/* Black w/ opacity */
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute; /* Or you could use float: right; */
    top: 10px; /* Adjust as needed */
    right: 10px; /* Align to the right */
    padding: 3px;
    cursor: pointer; /* Makes the button look clickable */
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
#resultRoom{
    position: relative;
    top: 0px;
    background-color: rgb(255, 255, 255,0.8); /* Black w/ opacity */
    border-radius: 28px;
}
.upload-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    margin: 10px;
    border: none;
    cursor: pointer;
    text-align: center;
}

.upload-btn:hover {
    background-color: #45a049;
}
    </style>
    
    <script>
        let fieldCounter = <?php echo count($formData); ?>;

        function addField() {
            const container = document.getElementById("fieldsContainer");
            const fieldDiv = document.createElement("div");
            fieldDiv.classList.add("field");
            fieldDiv.id = `field_${fieldCounter}`;

            fieldDiv.innerHTML = `
                <button type="button" class="delete-button" onclick="removeField(${fieldCounter})">X</button>
                <label>Label: <input type="text" name="fields[${fieldCounter}][label]" required></label>
                <label>Type: 
                    <select name="fields[${fieldCounter}][type]" onchange="toggleOptionsField(this, ${fieldCounter})">
                        <?php 
                            $queryForFields = "SELECT * FROM form_fields WHERE field_status = 1";
                            $fieldsOption = $conn->query($queryForFields);
                            while($rowField = $fieldsOption->fetch_assoc()) {
                                echo '<option value="'.$rowField['field_name'].'">'.$rowField['field_title'].'</option>';
                            }
                        ?>
                    </select>
                </label>
                <label>Name: <input type="text" name="fields[${fieldCounter}][name]" required></label>
                <label>Placeholder: <input type="text" name="fields[${fieldCounter}][placeholder]"></label>
                <div id="optionsField_${fieldCounter}" style="display: none;">
                <label>Options (comma-separated): <input type="text" name="fields[${fieldCounter}][options]"></label>
                </div>
                <label class="required-field">
                    <span>Required:</span> <input type="checkbox" name="fields[${fieldCounter}][required]">
                </label>
                <div class="sort-buttons">
                    <button type="button" class="move-up" onclick="moveUp(${fieldCounter})">▲ Move Up</button>
                    <button type="button" class="move-down" onclick="moveDown(${fieldCounter})">▼ Move Down</button>
                </div>
            `;

            container.appendChild(fieldDiv);
            fieldCounter++;
        }

        function removeField(index) {
            const fieldToRemove = document.getElementById(`field_${index}`);
            if (fieldToRemove) {
                fieldToRemove.remove();
            }
        }

        function moveUp(index) {
            const field = document.getElementById(`field_${index}`);
            if (field && field.previousElementSibling) {
                field.parentNode.insertBefore(field, field.previousElementSibling);
            }
        }

        function moveDown(index) {
            const field = document.getElementById(`field_${index}`);
            if (field && field.nextElementSibling) {
                field.parentNode.insertBefore(field.nextElementSibling, field);
            }
        }

        function toggleOptionsField(selectElement, fieldIndex) {
            const optionsField = document.getElementById(`optionsField_${fieldIndex}`);
            if (selectElement.value === 'select') {
                optionsField.style.display = 'block';
            } else {
                optionsField.style.display = 'none';
            }
        }
        

    </script>
<title>Manage Dynamic Form</title>
</head>
<body>
<div class="dashboard-content">
    <div class="form-container" style="margin-left: 280px;">
    <h1 style="color: black;" >Manage Form Fields</h1>
    
    <form  method="POST" id="saveForm">
        <input type="hidden" name="formId" value="<?php echo $formId; ?>">

        <div>
            <label>Form Name: <input type="text" name="formName" value="<?php echo isset($formName) ? htmlspecialchars($formName) : ''; ?>" required></label>
            <label>Form Slug: <input type="text" name="formSlug" value="<?php echo isset($formSlug) ? htmlspecialchars($formSlug) : ''; ?>" required></label>
            <label>Description: <input name="description" required value="<?php echo isset($formDescription) ? htmlspecialchars($formDescription):""; ?>"></label>
            <label>Status:
                <select name="status">
                    <option value="active" <?php echo (isset($formStatus) && $formStatus == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($formStatus) && $formStatus == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </label>
            <label style="font-weight: bold; display: flex; align-items: center; gap: 10px;">
    Background Color: 
    <input name="background_color" type="color"  
        value="<?php echo isset($formBgClr) ? htmlspecialchars($formBgClr) : null; ?>" 
        style="appearance: none; width: 65%; height: 35px; border: none; cursor: pointer; border-radius: 6px; padding: 0;margin-bottom:8px;
               background: none; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); transition: transform 0.2s ease;">
</label>

            <label>Background Image :
                <?php if (!empty($formBgImg) && isset($formBgImg)): ?>
                    <div style="display: flex; align-items: center; position: relative;">
        <img src="<?php echo '../admin/'.$formBgImg ?>"  id="selectedImagePreview"  alt="form-bgimage1" style="width: 130px; height: 120px; margin-left: 7px;"/>
        <!-- The X button to remove the image -->
        <button id="rmvImagePreview"  type="button" style=" text-align:center;position: absolute;  top: 2px; left: 109px; background-color: red; color: white; border: none; border-radius: 26%; width: 30px; height: 25px; font-size: 16px; cursor: pointer; z-index: 10;" class="delete-button" onclick="removeImage()">X</button>
    </div>
        <?php endif; ?>
        <div style="display: flex; align-items: center; position: relative;">
    <!-- The "X" button to remove the image -->
    <button type="button" id="rmvchoosedImagePreview" 
        style="text-align:center; display: none; position: absolute; top: 2px; left: 109px; background-color: red; color: white; border: none; border-radius: 26%; width: 24px; height: 25px; font-size: 16px; cursor: pointer; z-index: 10;" 
        class="delete-button"  onclick="removeImage()">X</button>

    <!-- Image preview -->
    <img src="" alt="form-bgimage" id="choosedImagePreview" 
        style="display: none; width: 130px; height: 120px; margin-left: 7px;"/>
</div>

    <input type="text" id="selectedImage" name="background_image" value="<?php  echo isset($formBgImg) ? htmlspecialchars($formBgImg):"" ?>" readonly placeholder="Double click on an image to select it">
 <!-- Upload Photos Button -->
 <button type="button" class="upload-btn" onclick="showImageUploadModal()">Upload Photo</button></label>

        <hr>

        <div id="fieldsContainer">
    <?php if (isset($formData) && is_array($formData)) : ?>
        <?php foreach ($formData as $index => $field) : ?>
            <div class="field" id="field_<?php echo $index; ?>">
                <button type="button" class="delete-button" onclick="removeField(<?php echo $index; ?>)">X</button>

                <!-- Label -->
                <label>
                    Label: 
                    <input type="text" name="fields[<?php echo $index; ?>][label]" 
                        value="<?php echo isset($field['label']) ? htmlspecialchars($field['label']) : ''; ?>" 
                        required>
                </label>

                <!-- Type -->
                <label>Type:
                    <select name="fields[<?php echo $index; ?>][type]" onchange="toggleOptionsField(this, <?php echo $index; ?>)">
                        <?php
                        if (isset($typeArray) && is_array($typeArray)) {
                            foreach($typeArray as $typeRow) {
                                if(isset($field['type']) && $typeRow['field_name'] == $field['type']){
                                    echo '<option value="'.$typeRow['field_name'].'" selected>'.htmlspecialchars($typeRow['field_title']).'</option>';
                                }
                                else {
                                    echo '<option value="'.$typeRow['field_name'].'">'.htmlspecialchars($typeRow['field_title']).'</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </label>

                <!-- Name -->
                <label>
                    Name: 
                    <input type="text" name="fields[<?php echo $index; ?>][name]" 
                        value="<?php echo isset($field['name']) ? htmlspecialchars($field['name']) : ''; ?>" required>
                </label>

                <!-- Placeholder -->
                <label>
                    Placeholder: 
                    <input type="text" name="fields[<?php echo $index; ?>][placeholder]" 
                        value="<?php echo isset($field['placeholder']) ? htmlspecialchars($field['placeholder']) : ''; ?>" required>
                </label>

                <!-- Options (Only show if the type is 'select') -->
                <div id="optionsField_<?php echo $index; ?>" 
        style="display: <?php echo isset($field['type']) && $field['type'] == 'select' ? 'block' : 'none'; ?>;">
        <label>Options (comma-separated): 
            <input type="text" name="fields[<?php echo $index; ?>][options]" 
                value="<?php echo isset($field['options']) && is_array($field['options']) 
                            ? htmlspecialchars(implode(",", $field['options'])) 
                            : ''; ?>">
        </label>
    </div>
                <label class="required-field">
                    <!-- Required Checkbox -->
                    <span>Required:</span>
                    <input type="checkbox" name="fields[<?php echo $index; ?>][required]" 
                        <?php echo isset($field['required']) && $field['required'] ? 'checked' : ''; ?>>
                </label>

                <!-- Sort Buttons -->
                <div class="sort-buttons">
                    <button type="button" class="move-up" onclick="moveUp(<?php echo $index; ?>)">▲ Move Up</button>
                    <button type="button" class="move-down" onclick="moveDown(<?php echo $index; ?>)">▼ Move Down</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


        <button type="button" class="add-field-button" onclick="addField()">+ Add Field</button>
        <button type="submit" class="save-button">💾 Save Form</button>
    </form>
    </div>
</div>
<div id="imageUploadModal" class="modal">
    <div class="modal-content">
    <span class="close" onclick="closeImageUploadModal()">&times;</span>
    <h2>Upload Image</h2>
    <form id="imageUploadForm" method="post" enctype="multipart/form-data">
        <input type="file" id="photos" name="room_image[]" accept="image/*" multiple>
        <input type="text" name="status" value="0" hidden>
        <button type="submit" id="imageUploadBtn">Upload</button>
    </form>
</div>

<div id="resultRoom">
                <?php
                $query = "Select * from media ORDER BY created_at desc;";
                $resultMedia = mysqli_query($conn,$query);
                if(mysqli_num_rows($resultMedia) > 0){
                    $index =0;
                    while($rows = mysqli_fetch_assoc($resultMedia)){
                        echo '<div class="mediaImg" style="padding: 10px; border: 1px solid #ddd; display: inline-block; margin: 10px;">
                          <input type="text" hidden name="image_path" id="imgPath" value ="'.$rows["image_path"].'">
                                <img src="/admin/'. $rows["image_path"] .'" alt="Image" style="width: 220px; height: 160px; object-fit: cover; border-radius: 8px;">
                              </div>';
                    }
                } else {
                    echo "<h2 style='text-align: center; color: #333;'>NO IMAGE UPLOADED</h2>";
                }
                

                ?>
                </div>
                </div>
                <div id="errorModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
    <div style="position: relative; margin: 20% auto; background-color: #fff; padding: 20px; width: 300px; border-radius: 5px; text-align: center;">
        <h3 style="color: #e74c3c;">Error</h3>
        <br>
        <p id="errorMessage" style="color: #e74c3c;"></p>
        <br>
        <button onclick="closeErrorModal()" style="background-color: #e74c3c; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;">Close</button>
    </div>
</div>
<div id="successModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
    
    <div style="position: relative; margin: 15% auto; background-color: #e8f5e9; padding: 25px; width: 350px; 
        border-radius: 10px; text-align: center; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); 
        border: 2px solid #388e3c; animation: fadeIn 0.4s ease-in-out;">
        
        <!-- Success Icon -->
        <div style="width: 60px; height: 60px; background-color: #388e3c; color: white; 
            font-size: 30px; font-weight: bold; line-height: 60px; text-align: center; 
            border-radius: 50%; margin: -50px auto 10px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);">
            ✓
        </div>

        <h3 style="color: #2e7d32; margin-bottom: 10px; font-family: Arial, sans-serif;">Success</h3>

        <hr style="border: 2px solid #388e3c; width: 100%;">

        <p id="successMessage" style="color: #333; font-size: 16px; font-family: Arial, sans-serif; 
            margin: 15px 0; padding: 10px; background: #c8e6c9; border-radius: 5px; box-shadow: inset 0px 1px 4px rgba(0,0,0,0.1);">
        </p>

        <!-- Close Button -->
        <button onclick="document.getElementById('successModal').style.display='none'" 
            style="background-color: #388e3c; color: white; border: none; padding: 10px 20px; 
            font-size: 14px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2); transition: 0.3s;">
            Close
        </button>

    </div>
</div>


<script>

// JavaScript to remove the image and reset the file input
function removeImage() {
    // Remove the image element
    const imagePreview = document.getElementById('selectedImagePreview');
    const choosedimagePreview = document.getElementById('choosedImagePreview');
    const choosedimagePreviewBtn = document.getElementById('rmvchoosedImagePreview');
    
    
    if(document.getElementById('rmvImagePreview')){
document.getElementById('rmvImagePreview').style.display = "none";
    }
    if (imagePreview) {
        imagePreview.src = ''; // Clear the image preview
        imagePreview.setAttribute('hidden', 'true');
    }
    if (choosedimagePreview) {
        choosedimagePreview.src = ''; // Clear the image preview
        choosedimagePreview.style.display ="none";
        choosedimagePreviewBtn.style.display = "none"
    }

    // Clear the hidden input field value
    document.getElementById('selectedImage').value = '';

    // Optionally, reset the file input (so the same file can be uploaded again)
    document.getElementById('imageInput').value = '';
}


    function showImageUploadModal() {
    document.getElementById("imageUploadModal").style.display = "block";
}

function closeImageUploadModal() {
    document.getElementById("imageUploadModal").style.display = "none";
}

function selectImage(imagePath) {
    document.getElementById("selectedImage").value = imagePath;
    closeImageUploadModal();
}
window.onload = function() {
if (localStorage.getItem("showModal") === "true") {
        // Show the modal automatically
        document.getElementById("imageUploadModal").style.display = "block";
        
        // Clear the flag so the modal doesn't show again after another reload
        localStorage.removeItem("showModal");
    }
};
const imgElements = document.getElementsByClassName("mediaImg");
Array.from(imgElements).forEach(imgDiv => {
    imgDiv.addEventListener("dblclick", (e) => {
        const imgDiv = e.currentTarget;
        
        // Access the hidden input field inside the clicked div
        const imgPathInput = imgDiv.querySelector("input[name='image_path']");
        
        // Get the value of the hidden input
        const imagePath = imgPathInput.value;
       path = document.getElementById("selectedImage").value=imagePath;
        const imagePreview = document.getElementById('choosedImagePreview'); 
        const imageRemoveBtn = document.getElementById('rmvchoosedImagePreview'); 

    if (imagePreview){
        imagePreview.src = "/admin/"+path; 
        imagePreview.style.display = 'block';
        imageRemoveBtn.style.display = 'block';
    }
    if (document.getElementById("rmvImagePreview")) {
        if(document.getElementById("selectedImagePreview")){
            document.getElementById("rmvImagePreview").style.display="none";
            document.getElementById("selectedImagePreview").style.display="none";
        }
   
} 
        var modal = document.getElementById("imageUploadModal");
        modal.style.display = "none";
        
    });
});
function showErrorModal() {
    // Display the modal
    document.getElementById("errorModal").style.display = "block";
}
function closeErrorModal() {
    document.getElementById("errorModal").style.display = "none";
}
function showSuccessImageUploadModal() {
    // Display the modal
    document.getElementById("successModal").style.display = "block";
    setTimeout(function() {
        window.location.reload();  // Reload the page
    }, 1000); 
}
document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting the traditional way

    const fileInput = document.getElementById('photos');
    const file = fileInput.files;

    const form =this;
    // Validate if a file has been selected

   
    if (file.length == "0") {
        return false; 
    }
    // Get the form data, including the file input
    const formData = new FormData(this);

    // Send the form data via Fetch API
    fetch('../admin/addMedia.php', {
        method: 'POST',
        body: formData  // Form data includes the file input and other form fields
    })
    .then(response => response.text())  // We expect a text response from PHP
    .then(data => {
        
        resAlreadyUploaded=data.includes("Connection failed to upload image");
if(resAlreadyUploaded){
    document.getElementById("errorMessage").textContent = "Image has been uploaded already.";
    showErrorModal();
    return;
}
        resTypeValidation=data.includes("Only JPEG, PNG files are allowed");
if(resTypeValidation){
    document.getElementById("errorMessage").textContent = "Only JPEG, PNG files are allowed";
    showErrorModal();
    return;
}
        resMaxSize=data.includes("File size exceeds the maximum limit of 2 MB");
if(resMaxSize){
    document.getElementById("errorMessage").textContent = "File size exceeds the maximum limit of 2 MB";
    showErrorModal();
    return;
}

document.getElementById("successMessage").textContent = "Image has been uploaded .";
        //window.location.href="form.php"; 
        showSuccessImageUploadModal();
        // document.getElementById('result').innerHTML = data; 
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
document.getElementById('saveForm').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting the traditional way

    const form = this;
    const formData = new FormData(form);  // Collect all form data, including fields and images

    // Validate that required fields are filled out
    const formName = form.querySelector('[name="formName"]');
    if (!formName.value) {
        alert("Form Name is required!");
        return;
    }

    const formSlug = form.querySelector('[name="formSlug"]');
    if (!formSlug.value) {
        alert("Form Slug is required!");
        return;
    }

    
    // Send the form data via Fetch API
    fetch('save_form.php', {
        method: 'POST',
        body: formData  // Form data includes all the fields and uploaded files (if any)
    })
    .then(response => response.text())  // We expect a text response from PHP
    .then(data => {

        console.log(data);
        
        
        // Handle different responses from the server
        if (data.includes("Error") || data.includes("Error updating form: ") ||data.includes("Error saving form: ")) {
            document.getElementById("errorMessage").textContent = data;
            showErrorModal();
        } else if (data.includes("Success:") || data.includes("Form updated successfully!")) {
            document.getElementById("successMessage").textContent = "Form has been saved successfully.";
            showSuccessImageUploadModal();  // Call a function to display a success modal or message
        } else {
            // Handle other unexpected responses if necessary
            document.getElementById("errorMessage").textContent = "An unexpected error occurred.";
            showErrorModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById("errorMessage").textContent = "There was an error with the submission.";
        showErrorModal();
    });
});



</script>
</body>
</html>
