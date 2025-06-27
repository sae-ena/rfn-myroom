<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- Primary Meta Tags -->
  <title>Casabo Room Finder - Best Room Rental & Accommodation in Nepal | Kathmandu, Lalitpur, Pokhara</title>
  <meta name="title" content="Casabo Room Finder - Best Room Rental & Accommodation in Nepal | Kathmandu, Lalitpur, Pokhara">
  <meta name="description" content="Find the perfect room, apartment, or accommodation in Nepal. Browse single rooms, 1BHK, 2BHK apartments in Kathmandu, Lalitpur, Pokhara. Best room rental platform with verified listings and easy booking.">
  <meta name="keywords" content="room rental Nepal, accommodation Kathmandu, apartment for rent Nepal, single room Kathmandu, 1BHK apartment Nepal, 2BHK for rent, room finder Nepal, student accommodation Kathmandu, budget room Nepal, furnished apartment Kathmandu, room booking Nepal, rental property Nepal, house for rent Kathmandu, flat rental Nepal, room sharing Kathmandu, affordable accommodation Nepal, luxury apartment Kathmandu, room search Nepal, property rental Kathmandu, room listing Nepal">
  <meta name="author" content="Casabo Room Finder">
  <meta name="robots" content="index, follow">
  <meta name="language" content="English">
  <meta name="revisit-after" content="7 days">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
  <meta property="og:title" content="Casabo Room Finder - Best Room Rental & Accommodation in Nepal">
  <meta property="og:description" content="Find the perfect room, apartment, or accommodation in Nepal. Browse single rooms, 1BHK, 2BHK apartments in Kathmandu, Lalitpur, Pokhara. Best room rental platform with verified listings and easy booking.">
  <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png">
  <meta property="og:site_name" content="Casabo Room Finder">
  <meta property="og:locale" content="en_US">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
  <meta property="twitter:title" content="Casabo Room Finder - Best Room Rental & Accommodation in Nepal">
  <meta property="twitter:description" content="Find the perfect room, apartment, or accommodation in Nepal. Browse single rooms, 1BHK, 2BHK apartments in Kathmandu, Lalitpur, Pokhara.">
  <meta property="twitter:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png">
  
  <!-- Additional SEO Meta Tags -->
  <meta name="geo.region" content="NP">
  <meta name="geo.placename" content="Kathmandu, Nepal">
  <meta name="geo.position" content="27.7172;85.3240">
  <meta name="ICBM" content="27.7172, 85.3240">
  <meta name="distribution" content="global">
  <meta name="rating" content="general">
  <meta name="theme-color" content="#4CAF50">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  
  <!-- Preconnect to external domains for performance -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Structured Data for Local Business -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Casabo Room Finder",
    "description": "Find the perfect room, apartment, or accommodation in Nepal. Browse single rooms, 1BHK, 2BHK apartments in Kathmandu, Lalitpur, Pokhara.",
    "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>",
    "logo": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png",
    "image": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Kathmandu",
      "addressCountry": "NP",
      "addressRegion": "Bagmati"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 27.7172,
      "longitude": 85.3240
    },
    "telephone": "+9779843400009",
    "email": "info@roomfinder.com",
    "priceRange": "$$",
    "openingHours": "Mo-Su 00:00-23:59",
    "sameAs": [
      "https://facebook.com/casaboroomfinder",
      "https://twitter.com/casaboroomfinder"
    ],
    "serviceArea": {
      "@type": "Country",
      "name": "Nepal"
    },
    "hasOfferCatalog": {
      "@type": "OfferCatalog",
      "name": "Room Rentals",
      "itemListElement": [
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Single Room Rental"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "1BHK Apartment Rental"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "2BHK Apartment Rental"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Budget Room Rental"
          }
        }
      ]
    }
  }
  </script>
  
  <!-- Structured Data for Website -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Casabo Room Finder",
    "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>",
    "description": "Find the perfect room, apartment, or accommodation in Nepal. Browse single rooms, 1BHK, 2BHK apartments in Kathmandu, Lalitpur, Pokhara.",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>?searchLocation={search_term_string}&searchRoomType={search_room_type}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>

  <link rel="stylesheet" href="css/style.css" />
  <script>
  (function() {
    const CSS_URL = '/css/style.css';
    const CSS_KEY = 'cachedStyleCSS';
    const CSS_EXPIRY = 24 * 60 * 60 * 1000; // 1 day
    function injectCSS(css) {
      let style = document.getElementById('dynamic-style');
      if (!style) {
        style = document.createElement('style');
        style.id = 'dynamic-style';
        document.head.appendChild(style);
      }
      style.textContent = css;
    }
    try {
      const cached = JSON.parse(localStorage.getItem(CSS_KEY));
      if (cached && Date.now() - cached.time < CSS_EXPIRY) {
        injectCSS(cached.css);
      } else {
        fetch(CSS_URL)
          .then(r => r.text())
          .then(css => {
            injectCSS(css);
            localStorage.setItem(CSS_KEY, JSON.stringify({css, time: Date.now()}));
          });
      }
    } catch (e) {}
  })();
  </script>
</head>

<body>
  <?php
require_once("helperFunction/SweetAlert.php");
  ?>
  <nav class="navbar">
    <div class="container">
      <a href="/" class="logo">Casabo Room Finder</a>
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
                                   <a href="#" class="dropbtn user-profile-btn">' . $_SESSION['user_name'] . '</a>
                  <div class="dropdown-content">
                   <form action="logout.php" method="POST">
    <input type="hidden" name="csrf_token" value="'.$_SESSION["csrf_token"].'">
   <button type="submit" class="logout-btn">
    Logout
</button>

</form>
                  </div>

                </li>';
        } else {
          // User is not logged in
          echo '<li><a href="login.php" class="btn">Login</a></li>';
        }
        require_once('admin/dbConnect.php');
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