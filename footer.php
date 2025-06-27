<footer>
  <div class="container">
    <div class="footer-container">
      <div class="footer-section">
        <h4>Casabo Room Finder - Nepal's Premier Accommodation Platform</h4>
        <p>Your trusted partner for finding the perfect room rental, apartment, and accommodation across Nepal. From budget rooms in Kathmandu to luxury apartments in Pokhara, we connect you with verified property dealers and landlords.</p>
        <div class="footer-social">
          <a href="https://facebook.com/casaboroomfinder" aria-label="Follow us on Facebook">Facebook</a>
          <a href="https://instagram.com/casaboroomfinder" aria-label="Follow us on Instagram">Instagram</a>
        </div>
      </div>
      
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#roomsTitle">Browse Rooms</a></li>
          <li><a href="#gallery">Gallery</a></li>
          <li><a href="?searchLocation=Kathmandu&serachRoom=Search">Rooms in Kathmandu</a></li>
          <li><a href="?searchLocation=Lalitpur&serachRoom=Search">Rooms in Lalitpur</a></li>
          <li><a href="?searchLocation=Pokhara&serachRoom=Search">Rooms in Pokhara</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Room Types</h4>
        <ul>
          <li><a href="?searchRoomType=singleRoom&serachRoom=Search">Single Room Rental</a></li>
          <li><a href="?searchRoomType=1BHK&serachRoom=Search">1BHK Apartments</a></li>
          <li><a href="?searchRoomType=2BHK&serachRoom=Search">2BHK Apartments</a></li>
          <li><a href="?searchRoomType=Budget&serachRoom=Search">Budget Rooms</a></li>
          <li><a href="?searchRoomType=apartment&serachRoom=Search">Luxury Apartments</a></li>
          <li><a href="?searchRoomType=Double&serachRoom=Search">Double Rooms</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Contact Us</h4>
        <ul>
          <li><i class="fas fa-phone"></i> <a href="tel:+9779843400009">Phone: +977 9843400009</a></li>
          <li><i class="fas fa-envelope"></i> <a href="mailto:info@roomfinder.com">Email: info@roomfinder.com</a></li>
          <li><i class="fas fa-map-marker-alt"></i> Location: Kathmandu, Nepal</li>
          <li><i class="fas fa-clock"></i> Support: 24/7 Available</li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="footer-bottom-content">
        <p>&copy; <?php echo date("Y"); ?> Casabo Room Finder. All Rights Reserved. | Best Room Rental Platform in Nepal</p>
        <div class="footer-links">
          <a href="/privacy-policy">Privacy Policy</a>
          <a href="/terms-of-service">Terms of Service</a>
          <a href="/sitemap.xml">Sitemap</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- SEO Structured Data for Organization -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Casabo Room Finder",
  "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>",
  "logo": "<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+9779843400009",
    "contactType": "customer service",
    "areaServed": "NP",
    "availableLanguage": "English"
  },
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Kathmandu",
    "addressCountry": "NP",
    "addressRegion": "Bagmati"
  },
  "sameAs": [
    "https://facebook.com/casaboroomfinder",
    "https://twitter.com/casaboroomfinder",
    "https://instagram.com/casaboroomfinder"
  ]
}
</script>

<?php
if (isset($searchResult) && is_array($searchResult)) {
  // Instead of reloading the page, just scroll to the element with ID 'roomsTitleSearch'
  echo "<script>
          window.onload = function() {
              document.getElementById('roomsTitleSearch').scrollIntoView({
                  behavior: 'smooth', // Smooth scrolling
                  block: 'start' // Scroll to the top of the element
              });
          }
        </script>";
}
?>
<script>
let btn = document.getElementById("logoutBtn");
if (btn) {
    btn.onclick = function(event) {
        // Prevent the default action of the link (redirection)
        event.preventDefault();
        
        // Show the confirmation dialog
        let confirmLogout = confirm("Are you sure you want to logout?");
        
        // If the user clicks "OK", redirect to the logout page (e.g., dbConnect.php or a logout script)
        if (confirmLogout) {
            window.location.href = "admin/logout.php"; // or your logout script
        }
    };
}
</script>

   
  </body>
</html>