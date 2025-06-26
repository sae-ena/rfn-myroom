<?php


require "../helperFunction/CheckLogin.php";
CheckLogin::islogin();


if(stripos($_SERVER['SCRIPT_NAME'], "/dynaform") !== false){
    echo'<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="../admin/style.css">
        <link rel="stylesheet" href="../admin/dashboard.css">
        <link rel="stylesheet" href="../admin/form.css">
    </head>';
}
else{

    echo'<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="form.css">
    </head>';
    }

echo'<body>
    <div class="dashboard">
        <!-- Sidebar/Navbar -->
        <nav class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="/admin/dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="/admin/roomTable.php" class="nav-link">Rooms</a></li>
                <li><a href="/admin/booking.php" class="nav-link" data-target="bookings">Bookings</a></li>
                <li><a href="/admin/userTable.php" class="nav-link" data-target="users">Users</a></li>
                <li><a href="/admin/approve.php" class="nav-link" data-target="settings">Approve List</a></li>
                <li><a href="paymentHistory.php" class="nav-link" data-target="paymentHistory">Payment & Booking History</a></li>
                <li><a href="/admin/media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                <li><button id="mainSettingsToggle">Main Settings &#9660;</button></li>
                <ul id="mainSettingsSubmenu" style="display:none;list-style:none;padding-left:20px;margin:0;">
                <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media">Form  Manager</a></li>
                    <li><a href="/admin/emailTemplate.php" class="nav-link" data-target="email-template">Email Template</a></li>
                    <li><a href="/admin/backendSetting.php" class="nav-link" data-target="backend-setting">Backend Setting</a></li>
                </ul>
                <li><a href="/admin/dbConnect.php" class="nav-link" id="logoutBtn" >Logout</a></li>
            </ul>
        </nav>
        </div>';

?>
<script>
let btn = document.getElementById("logoutBtn");
btn.onclick = function(event) {
    
    // <li><a href="/admin/media.php" class="nav-link" data-target="media" disable>Media  Manager</a></li>
                // <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media" disabled>Form  Manager</a></li>
    // <li><a href="media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                // <li><a href="/admin/media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                // <li><a href="/admin/formManagerTable.php" class="nav-link" data-target="media">Form  Manager</a></li>
            // Prevent the default action of the link (redirection)
            event.preventDefault();
            
            // Show the confirmation dialog
            let confirmLogout = confirm("Are you sure you want to logout?");
            
            // If the user clicks "OK", redirect to the logout page (e.g., dbConnect.php or a logout script)
            if (confirmLogout) {
                window.location.href = "logout.php"; // or your logout script
            }
        };

// Toggle Main Settings
const mainSettingsToggle = document.getElementById('mainSettingsToggle');
const mainSettingsSubmenu = document.getElementById('mainSettingsSubmenu');
mainSettingsToggle.addEventListener('click', function() {
    if (mainSettingsSubmenu.style.display === 'none') {
        mainSettingsSubmenu.style.display = 'block';
        mainSettingsToggle.innerHTML = 'Main Settings &#9650;';
    } else {
        mainSettingsSubmenu.style.display = 'none';
        mainSettingsToggle.innerHTML = 'Main Settings &#9660;';
    }
});
</script>

<style>
.sidebar {
    height: 100vh;
    overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE 10+ */
}
.sidebar::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* Chrome/Safari/Webkit */
}
</style>
