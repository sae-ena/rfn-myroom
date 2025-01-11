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
   if(! isset($_SESSION)){
    session_start();
  }
    ?>
    <nav class="navbar">
      <div class="container">
        <a href="#" class="logo">Casabo Room Finder</a>
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

            echo'<li><a href="login" id="logoutBtn" class="btn">Logout</a></li>';
    } else{

      echo'<li><a href="login.php" class="btn">Login</a></li>';
    }  ?>
        </ul>
      </div>
    </nav>