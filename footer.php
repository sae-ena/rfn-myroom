<footer>
  <div class="footer-container">
    <div class="footer-section">
      <h4>Room Finder</h4>
      <p>Your go-to app for finding and booking rooms online.</p>
    </div>
    
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#roomsTitle">Browse Rooms</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">FAQs</a></li>
      </ul>
    </div>
    
    <div class="footer-section">
      <h4>Contact Us</h4>
      <ul>
        <li><a href="tel:+123456789">Phone: +977 9843400009</a></li>
        <li><a href="mailto:info@roomfinder.com">Email: info@roomfinder.com</a></li>
      </ul>
    </div>
    
   
  </div>
  
  <div class="footer-bottom">
    <p>&copy; 2024 Room Finder. All Rights Reserved.</p>
  </div>
</footer>
<?php
require('helperFunction/SweetAlert.php');
?>
<script>
let btn = document.getElementById("logoutBtn");
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

</script>

   
  </body>
</html>