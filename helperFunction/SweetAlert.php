<style>
/* Danger Notification Styles */
.danger-notify {
    position: fixed;
    top: 20px; /* Distance from the top */
    right: 20px; /* Distance from the right */
    background-color: #f8d7da; /* Light red background */
    color: #721c24; /* Dark red text */
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #f5c6cb; /* Red border */
    font-family: Arial, sans-serif;
    font-size: 16px;
    z-index: 1000; /* Ensure it's on top of other elements */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    opacity: 1;
    transition: opacity 1s ease-out;
}
.success-notify {
    position: fixed;
    top: 20px; /* Distance from the top */
    right: 20px; /* Distance from the right */
    background-color: #66BB96; /* Light red background */
    color: #f5f5f5; /* Dark red text */
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #f5c6cb; /* Red border */
    font-family: Arial, sans-serif;
    font-size: 16px;
    z-index: 1000; /* Ensure it's on top of other elements */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    opacity: 1;
    transition: opacity 1s ease-out;
}
</style>
<script >
        function clearFormError() {
            setTimeout(function() {
                document.querySelector('.danger-notify ').style.display = 'none';
            }, 4900); // 10000 milliseconds = 10 seconds
        }
        function clearSuccessAlert() {
            setTimeout(function() {
                document.querySelector('.success-notify ').style.display = 'none';
            }, 4900); // 10000 milliseconds = 10 seconds
        }
    </script>
<?php if (isset($form_error)): ?>
    <div class="danger-notify">
        <span><?php echo $form_error; ?></span>
    </div>
    <script>
        clearFormError();
        </script>
<?php endif; ?>
    
    <?php if (isset($successfullyRoomAdded)): ?>
        <div class="success-notify">
            <span><?php echo $successfullyRoomAdded; ?></span>
        </div>
        <script>
        clearSuccessAlert();
        </script>
        <?php endif; ?>