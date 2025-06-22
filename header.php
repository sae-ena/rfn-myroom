<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Casabo Room Finder </title>

  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <?php
require("helperFunction/SweetAlert.php");
require_once('helperFunction/helpers.php');
  ?>
  <nav class="navbar">
    <div class="container">
      <a href="index.php" class="logo">Casabo Room Finder</a>
      <!-- Hamburger menu for mobile -->
      <div class="hamburger" id="hamburger-menu" tabindex="0" aria-label="Toggle navigation" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="#roomsTitle">Services</a></li>
        <li><a href="#about">About</a></li>
        <li class="dropdown">
          <a href="#" class="dropbtn">More</a>
          <div class="dropdown-content">
            <?php
             if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === "user") {?>
            <a href="myBooking.php">My Booking</a><?php } ?>
            <a href="#contact">Contact</a>
          </div>
        </li>
        <?php
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === "user") {
          // User is logged in
          echo '<li class="dropdown">
                                   <a href="#" class="dropbtn" style="background-color:rgb(255, 115, 0); color: white; margin-left:8px; font-size: 16px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; padding: 10px 20px; border-radius: 5px;">' . $_SESSION['user_name'] . '</a>
                  <div class="dropdown-content">
                   <form action="logout.php" method="POST">
    <input type="hidden" name="csrf_token" value="'.$_SESSION["csrf_token"].'">
   <button type="submit" style="
    width: 80%; 
    height: 100%; 
    background-color:rgb(253, 136, 20); 
    color: white; 
    border: none; 
    padding: 15px; 
    font-size: 15px; 
    cursor: pointer; 
    border-radius: 25px;
    transition: background 0.3s ease;
" 
    onmouseover="this.style.backgroundColor="#cc0000""
    onmouseout="this.style.backgroundColor="#ff4d4d"">
    Logout
</button>

</form>
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
  <script>
    // Hamburger menu toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
      var hamburger = document.getElementById('hamburger-menu');
      var navLinks = document.querySelector('.nav-links');
      if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
          navLinks.classList.toggle('active');
          hamburger.classList.toggle('active'); // For hamburger animation
          var expanded = hamburger.getAttribute('aria-expanded') === 'true';
          hamburger.setAttribute('aria-expanded', !expanded);
        });
        // Optional: close menu when a link is clicked (for single-page feel)
        var navLinkEls = document.querySelectorAll('.nav-links a');
        navLinkEls.forEach(function(link) {
          link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
              navLinks.classList.remove('active');
              hamburger.classList.remove('active');
              hamburger.setAttribute('aria-expanded', 'false');
            }
          });
        });
      }
    });
  </script>