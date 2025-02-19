<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Casabo Resort </title>

  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <?php
require("helperFunction/SweetAlert.php");
require_once('helperFunction/helpers.php');
  if (!isset($_SESSION)) {
    session_start();
  }
  ?>
  <nav class="navbar">
    <div class="container">
      <a href="index.php" class="logo">Casabo Room Finder</a>
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="#roomsTitle">Services</a></li>
        <li><a href="#about">About</a></li>
        <li class="dropdown">
          <a href="#" class="dropbtn">More</a>
          <div class="dropdown-content">
            <a href="myBooking.php">My Booking</a>
            <a href="#contact">Contact</a>
          </div>
        </li>
        <?php
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === "user") {
          // User is logged in
          echo '<li class="dropdown">
                                   <a href="#" class="dropbtn" style="background-color:rgb(255, 115, 0); color: white; font-size: 16px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; padding: 10px 20px; border-radius: 5px;">' . $_SESSION['user_name'] . '</a>
                  <div class="dropdown-content">
                    <a href="http://localhost:8000/login" id="logoutBtn" style="color:rgb(225, 255, 0);">Logout</a>
                  </div>

                </li>';
        } else {
          // User is not logged in
          echo '<li><a href="login.php" class="btn">Login</a></li>';
        }
        require('admin/dbConnect.php');
        ?>
      </ul>
    </div>
  </nav>