<?php
require "../helperFunction/CheckLogin.php";
CheckLogin::islogin();

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
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar/Navbar -->
        <nav class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="roomTable.php" class="nav-link">Rooms</a></li>
                <li><a href="booking.php" class="nav-link" data-target="bookings">Bookings</a></li>
                <li><a href="userTable.php" class="nav-link" data-target="users">Users</a></li>
                <li><a href="approve.php" class="nav-link" data-target="settings">Approve List</a></li>
                <li><a href="media.php" class="nav-link" data-target="media">Media  Manager</a></li>
                <li><a href="dbConnect.php" class="nav-link" id="logoutBtn" >Logout</a></li>
            </ul>
        </nav>
        </div>';

?>
<script>
let btn = document.getElementById("logoutBtn");
btn.onclick = function(event) {
    // <li><a href="media.php" class="nav-link" data-target="media">Media  Manager</a></li>
            // Prevent the default action of the link (redirection)
            event.preventDefault();
            
            // Show the confirmation dialog
            let confirmLogout = confirm("Are you sure you want to logout?");
            
            // If the user clicks "OK", redirect to the logout page (e.g., dbConnect.php or a logout script)
            if (confirmLogout) {
                window.location.href = "logout.php"; // or your logout script
            }
        };

</script>
