<?php
  if (!isset($_SESSION)) {
    session_start();
  }
require('header.php') ; 
require_once('helperFunction/helpers.php');

if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['serachRoom'])){
    $searchLocation = $_GET['searchLocation'];
    $searchType = $_GET['searchRoomType'] ?? "";
    
    $searchResult = getRoomData($searchType, $searchLocation);
    if(! $searchResult){
       // Try searching with just location if both parameters were provided
       if (!empty($searchLocation) && !empty($searchType)) {
           $searchResult = getRoomData("", $searchLocation);
           if ($searchResult) {
               $form_error = "No rooms found with type '$searchType' in '$searchLocation'. Showing all rooms in '$searchLocation' instead.";
           } else {
               $form_error = "No rooms found in '$searchLocation'. Please try a different location or room type.";
           }
       } else {
           $form_error = "No Room Found. Please try different search criteria.";
       }
    }
   
}

?>
<!-- Hero Section Start -->
<section class="hero">
    <div class="hero-content">
        <h1>Find Your Perfect Place in Nepal</h1>
        <p>Discover the best room rentals, apartments, and accommodation in Kathmandu, Lalitpur, Pokhara, and across Nepal. From budget rooms to luxury apartments.</p>
        <div class="search-form-container">
            <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" class="search-form">
                <input type="text" name="searchLocation" placeholder="Enter Location (e.g., Kathmandu, Lalitpur, Pokhara, Chitwan)" class="location-input" />
                <select class="room-type-input" name="searchRoomType">
                    <option value="" selected disabled>Room Type</option>
                    <option value="singleRoom">Single Room</option>
                    <option value="1BHK">1BHK Apartment</option>
                    <option value="2BHK">2BHK Apartment</option>
                    <option value="apartment">Apartment</option>
                    <option value="Double">Double Room</option>
                    <option value="Budget">Budget Room</option>
                </select>
                <button type="submit" class="search-btn" name="serachRoom">Search Rooms</button>
            </form>
            <div style="margin-top: 15px; font-size: 0.9rem; color: #666; text-align: center;">
                <p style="font-size: 0.8rem; margin-top: 10px;"><em>Tip: You can search by location only, room type only, or both together</em></p>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->

<!-- Popular Locations Section Start -->
<section class="popular-locations">
    <div class="container">
        <h2>Popular Locations in Nepal</h2>
        <div class="location-grid">
            <div class="location-card">
                <h3>Kathmandu</h3>
                <p>Capital city with diverse accommodation options from budget rooms to luxury apartments</p>
                <a href="?searchLocation=Kathmandu&serachRoom=Search" class="location-link">Find Rooms in Kathmandu</a>
            </div>
            <div class="location-card">
                <h3>Lalitpur (Patan)</h3>
                <p>Historic city with traditional and modern room rental options</p>
                <a href="?searchLocation=Lalitpur&serachRoom=Search" class="location-link">Find Rooms in Lalitpur</a>
            </div>
            <div class="location-card">
                <h3>Pokhara</h3>
                <p>Tourist destination with scenic views and comfortable accommodation</p>
                <a href="?searchLocation=Pokhara&serachRoom=Search" class="location-link">Find Rooms in Pokhara</a>
            </div>
            <div class="location-card">
                <h3>Chitwan</h3>
                <p>Wildlife destination with affordable room rental options</p>
                <a href="?searchLocation=Chitwan&serachRoom=Search" class="location-link">Find Rooms in Chitwan</a>
            </div>
        </div>
    </div>
</section>
<!-- Popular Locations Section End -->

<!-- About Us Section Start -->
<div class="container" id="about">
    <div class="container-aboutUs">
        <div class="imageSection">
            <!-- Background image is set in CSS -->
        </div>
        <div class="aboutSectionLeft">
            <h2>About Casabo Room Finder - Nepal's Premier Room Rental Platform</h2>
            <p>
                Welcome to Casabo Room Finder, Nepal's leading platform for finding the perfect accommodation. Founded by passionate BCA students in Kathmandu, we connect travelers, students, and professionals with the best room rentals, apartments, and accommodation options across Nepal. Whether you're looking for a budget room in Kathmandu, a luxury apartment in Lalitpur, student accommodation in Pokhara, or a furnished apartment in Chitwan, we've got you covered.
            </p>
            <p>
                Our platform specializes in single room rentals, 1BHK and 2BHK apartments, budget accommodations, and luxury properties. We work with verified property dealers and landlords to ensure you get authentic listings with transparent pricing. From affordable room sharing options to premium furnished apartments, find your ideal home with ease through our user-friendly search interface.
            </p>
        </div>
    </div>
</div>
<!-- About Us Section End -->

<!-- Room Types Section Start -->
<section class="room-types-section">
    <div class="container">
        <h2>Types of Accommodation We Offer</h2>
        <div class="room-types-grid">
            <div class="room-type-card">
                <h3>Single Room Rental</h3>
                <p>Perfect for students and solo travelers. Affordable single room options across Nepal with basic amenities.</p>
                <a href="?searchRoomType=singleRoom&serachRoom=Search" class="room-type-link">Browse Single Rooms</a>
            </div>
            <div class="room-type-card">
                <h3>1BHK Apartments</h3>
                <p>Ideal for small families and couples. Spacious 1BHK apartments with modern amenities in prime locations.</p>
                <a href="?searchRoomType=1BHK&serachRoom=Search" class="room-type-link">Browse 1BHK Apartments</a>
            </div>
            <div class="room-type-card">
                <h3>2BHK Apartments</h3>
                <p>Perfect for families. Large 2BHK apartments with multiple bedrooms, kitchen, and living areas.</p>
                <a href="?searchRoomType=2BHK&serachRoom=Search" class="room-type-link">Browse 2BHK Apartments</a>
            </div>
            <div class="room-type-card">
                <h3>Budget Rooms</h3>
                <p>Economical accommodation options for budget-conscious travelers and students.</p>
                <a href="?searchRoomType=Budget&serachRoom=Search" class="room-type-link">Browse Budget Rooms</a>
            </div>
        </div>
    </div>
</section>
<!-- Room Types Section End -->

<!-- Interior Info Section Start -->
<div class="interior-info-section">
    <div class="container">
        <div class="title">
            <h4 class="text-center fs-2 fw-bolder">Our Premium Accommodation Features</h4>
        </div>
        <div class="interior-content">
            <div class="interior-image">
                <img src="/admin/uploads/67ae1392c5b2a_New-Furnished-Apartment.png" alt="Furnished Apartment in Nepal - Modern Interior with Premium Amenities" />
            </div>
            <div class="interior-text">
                <p class="fs-5">
                Discover the best room listings from trusted dealers across Nepal. Each listing includes comprehensive details like room type, competitive pricing, modern amenities, and prime locations, along with high-quality images. Our easy-to-use interface makes finding your perfect accommodation simple and hassle-free.
                </p>
                <h3>Premium Amenities & Services</h3>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Easy Online Booking: Book rooms with a few clicks from anywhere in Nepal</li>
                    <li><i class="fas fa-check-circle"></i> Availability Notifications: Get alerts for room availability or price drops</li>
                    <li><i class="fas fa-check-circle"></i> Verified Dealer Profiles & Reviews: Read authentic reviews and ratings of property dealers</li>
                    <li><i class="fas fa-check-circle"></i> Full-length mirror with LED lighting for modern comfort</li>
                    <li><i class="fas fa-check-circle"></i> Flat-screen TV with international programming</li>
                    <li><i class="fas fa-check-circle"></i> Multiple power outlets including USB ports for all your devices</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 Customer Support for all your accommodation needs</li>
                    <li><i class="fas fa-check-circle"></i> Secure Payment Options for safe transactions</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Interior Info Section End -->

<!-- Gallery Section Start -->
<div class="gallery_section p-5" id="gallery">
    <div class="container">
        <div class="title">
            <h4>Accommodation Gallery - See Our Premium Rooms & Apartments</h4>
        </div>
        <?php
        $query = "SELECT * FROM media WHERE status = 1 LIMIT 6"; // Fetch only active images, limit for design
        $result = $conn->query($query);
        ?>
        <?php if ($result->num_rows > 0): ?>
            <div class="gallery-grid">
                <?php while ($media = $result->fetch_assoc()): ?>
                    <div class="gallery-item">
                        <img src="/admin/<?php echo htmlspecialchars($media['image_path']); ?>" alt="Premium Room and Apartment Gallery - Nepal Accommodation" />
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No images found in the gallery.</p>
        <?php endif; ?>
    </div>
</div>
<!-- Gallery Section End -->

<!-- SEO Content Section Start -->
<section class="seo-content-section">
    <div class="container">
        <h2>Why Choose Casabo Room Finder for Your Accommodation in Nepal?</h2>
        <div class="seo-content-grid">
            <div class="seo-content-card">
                <h3>Best Room Rental Platform in Nepal</h3>
                <p>Casabo Room Finder is your trusted partner for finding the perfect accommodation across Nepal. Whether you're a student looking for affordable room sharing in Kathmandu, a professional seeking a luxury apartment in Lalitpur, or a family searching for a spacious 2BHK in Pokhara, we have verified listings to meet your needs.</p>
            </div>
            <div class="seo-content-card">
                <h3>Comprehensive Property Search</h3>
                <p>Our advanced search functionality allows you to find rooms by location, type, and budget. Search for single rooms, 1BHK apartments, 2BHK flats, budget accommodations, or luxury properties. We cover all major cities including Kathmandu, Lalitpur, Pokhara, Chitwan, and more.</p>
            </div>
            <div class="seo-content-card">
                <h3>Verified Listings & Trusted Dealers</h3>
                <p>All our room listings come from verified property dealers and landlords. We ensure authenticity, transparent pricing, and quality accommodation options. Read reviews, check ratings, and make informed decisions about your rental property in Nepal.</p>
            </div>
            <div class="seo-content-card">
                <h3>Student-Friendly Accommodation</h3>
                <p>Specializing in student accommodation across Nepal, we offer budget-friendly room options near universities and colleges. Find affordable single rooms, shared accommodations, and student-friendly apartments in prime educational districts.</p>
            </div>
        </div>
    </div>
</section>
<!-- SEO Content Section End -->

<?php require('roomData.php'); ?>

<?php require('footer.php') ; ?>