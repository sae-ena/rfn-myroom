<?php
  if (!isset($_SESSION)) {
    session_start();
  }
require('header.php') ; 

if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['serachRoom'])){
    $searchLocation = $_GET['searchLocation'];
    $searchType = $_GET['searchRoomType'] ?? "";
    $searchResult = getRoomData($searchLocation,$searchType);
    if(! $searchResult){
       $form_error = "No Room Found"; 
    
    }
   
}

?>
<!-- Hero Section Start -->
<section class="hero">
    <div class="hero-content">
        <h1>Find Your Perfect Place</h1>
        <p>Search for locations and room types that suit your needs.</p>
        <div class="search-form">
        <form method="get"action="<?php echo $_SERVER['PHP_SELF'];?>">
        <input type="text" name="searchLocation" placeholder="Enter Location" class="location-input" />
        <select class="room-type-input" name="searchRoomType">
            <option value="" selected disabled>Room Type</option>
            <option value="singleRoom">Single Room</option>
            <option value="2BHK">Double Room</option>
            <option value="1BHK">1BHK</option>
            <option value="apartment">Apartment</option>
        </select>
        <button class="search-btn" name="serachRoom">Search</button>
    </form>
    </div>
    </div>
</section>



<div class="container-aboutUs" id="about">
    <div class="imageSection">

    </div>
    <div class="aboutSectionLeft">
        <h2 class="fs-2">About Us</h2>

        <p>
            Welcome to Casabo Room Finder, a new venture brought to life by a team of passionate BCA students, located
            in the heart of Kathmandu, Nepal. We aim to offer a fresh perspective on travel accommodation, blending
            modern technology with traditional Nepalese hospitality. Our platform is designed to help you discover the
            perfect place to stay, whether you're looking for a luxury retreat, a family-friendly escape, or a cozy
            business trip haven. With a user-friendly interface and tailored options, we make it easy to find rooms that
            match your preferences, all while supporting local businesses. Surrounded by the rich cultural heritage of
            Nepal and scenic beauty, Casabo Room Finder is more than just a booking platform—it's your gateway to
            unforgettable experiences in one of the world’s most captivating destinations. Let us help you unlock the
            best stays, offering personalized recommendations, reliable services, and seamless bookings that ensure your
            trip is stress-free and memorable.
        </p>
    </div>


</div>


<div class="interior-info-section">
    <div class="title">
        <h4 class="text-center fs-2 fw-bolder">Our Interior</h4>
    </div>
    <div class="container-md container-fluid-lg d-flex justify-content-around mt-4 gap-5">
        <div class="col-5">
            <img src="/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png" alt="" width="100%" style="border-radius: 21px;" />
        </div>
        <div class="col-6">
            <div class="texts">
                <p class="fs-5">
                Our platform helps you discover the best room listings from trusted dealers. Each listing includes important details like room type, price, amenities, and location, along with high-quality images.Our easy-to-use interface makes the process simple and hassle-free.
                </p>

                <ul class="fs-5">
                    <h3>Amenities</h3>
                    <li>Easy Online Booking: Book rooms with a few clicks.</li>
                    <li>Availability Notifications: Get alerts for room availability or price drops.</li>
                    <li>Dealer Profiles & Reviews: Read reviews and ratings of dealers.</li>
                    <li>Full-length mirror with LED lighting.</li>
                    <li>Flat-screen TV with international programming.</li>
                    <li>
                        Outlets for personal electronics, including multiple plug and
                        socket types (USB).
                    </li>
                    <li>Smart TV features.</li>
                </ul>
                
            </div>
        </div>
    </div>
</div>


<div class="gallery_section p-5" id="gallery">
    <div class="title mt-4">
        <h4
            style="text-align: center; font-size: 33px; font-weight: bold; color: #333; text-transform: uppercase; letter-spacing: 2px; margin-top: 30px; padding: 10px 0; background-color:rgb(255, 145, 0); border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            Gallery
        </h4>
    </div>
    <?php
$query = "SELECT * FROM media WHERE status = 1"; // Fetch only active images
$result = $conn->query($query);
?>

<div class="container">
    <div class="gallery-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($media = $result->fetch_assoc()): ?>
                <div class="gallery-item">
                    <img src="/admin/<?php echo $media['image_path']; ?>" alt="Gallery Image"  />
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No images found in the gallery.</p>
        <?php endif; ?>
    </div>
</div>


</div>



<script>
  window.onload = function() {
    var image = document.querySelector('.gallery-item img');
    var container = document.querySelector('.gallery-item');

    function adjustImageFit() {
      var imageRatio = image.naturalWidth / image.naturalHeight;
      var containerRatio = container.offsetWidth / container.offsetHeight;

      // If the image ratio is greater than the container ratio, use contain
      if (imageRatio > containerRatio) {
        image.style.objectFit = 'contain';
      } else {
        image.style.objectFit = 'cover';
      }
    }

    adjustImageFit(); // Adjust on page load
    window.onresize = adjustImageFit; // Adjust on window resize
  }
</script>
    <?php require('roomData.php'); ?>

<?php require('footer.php') ; ?>