<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once('helperFunction/helpers.php');
require('admin/dbConnect.php'); // Needed for getRoomData

if (
    $_SERVER['REQUEST_METHOD'] == "GET" &&
    isset($_GET['searchRoomType']) || isset($_GET['searchLocation'])
) {
    $searchLocation = trim($_GET['searchLocation'] ?? "");
    $searchType = trim($_GET['searchRoomType'] ?? "");
        $searchResult = getRoomData($searchType, $searchLocation);
        if (!$searchResult) {
            if (!isset($_GET['error'])) {
                if (!empty($searchLocation) && !empty($searchType)) {
                    $searchResult = getRoomData("", $searchLocation);
                    if ($searchResult) {
                        $errorMessage = "No {$searchType} rooms found in {$searchLocation}. Showing all available rooms in {$searchLocation} instead.";
                    } else {
                        $errorMessage = "No rooms found in '{$searchLocation}'. Please try a different location or room type.";
                    }
                } elseif (!empty($searchLocation)) {
                    $errorMessage = "No rooms found in '{$searchLocation}'. Please try a different location or check back later.";
                } elseif (!empty($searchType)) {
                    $errorMessage = "No {$searchType} rooms available at the moment. Please try a different room type or location.";
                } else {
                    $errorMessage = "No rooms found. Please try different search criteria.";
                }
                $redirectUrl = $_SERVER['PHP_SELF'];
                $params = [];
                if (!empty($searchLocation)) {
                    $params[] = "searchLocation=" . urlencode($searchLocation);
                }
                if (!empty($searchType)) {
                    $params[] = "searchRoomType=" . urlencode($searchType);
                }
                $paramString = $params ? ('&' . implode('&', $params)) : '';
                header("Location: $redirectUrl?error=" . urlencode($errorMessage) . $paramString);
                exit();
            }
        }
    
}
require('header.php');

// Get error message from URL parameter
$errorMessage = isset($_GET['error']) ? $_GET['error'] : null;

?>
<!-- Hero Section Start -->
<section class="hero">
    <div class="hero-content">
        <h1>Find Your Perfect Place in Nepal</h1>
         <div class="search-form-container">
            <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" class="search-form" id="searchForm">
                <input type="text" name="searchLocation" placeholder="Enter Location (e.g., Kathmandu, Lalitpur, Pokhara, Chitwan)" class="location-input" value="<?php echo isset($_GET['searchLocation']) ? htmlspecialchars($_GET['searchLocation']) : ''; ?>" />
                <select class="room-type-input" name="searchRoomType">
                    <option value="" selected disabled>Room Type</option>
                    <option value="singleRoom" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == 'singleRoom') ? 'selected' : ''; ?>>Single Room</option>
                    <option value="1BHK" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == '1BHK') ? 'selected' : ''; ?>>1BHK Apartment</option>
                    <option value="2BHK" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == '2BHK') ? 'selected' : ''; ?>>2BHK Apartment</option>
                    <option value="apartment" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
                    <option value="Double" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == 'Double') ? 'selected' : ''; ?>>Double Room</option>
                    <option value="Budget" <?php echo (isset($_GET['searchRoomType']) && $_GET['searchRoomType'] == 'Budget') ? 'selected' : ''; ?>>Budget Room</option>
                </select>
                <button type="submit" class="search-btn" name="serachRoom" id="searchBtn">
                    <span class="search-btn-text">Search Rooms</span>
                    <span class="search-btn-loading" style="display: none;">Searching...</span>
                </button>
            </form>
            
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
            <h2>About Casabo Room Finder</h2>
            <p>
                Nepal's leading platform for finding the perfect accommodation.We connect travelers, students, and professionals with the best room rentals, apartments, and accommodation options across Nepal. Whether you're looking for a budget room in Kathmandu, a luxury apartment in Lalitpur or student accommodation in Pokhara we've got you covered.
            </p>
            <br>
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

<?php if (isset($errorMessage)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        notificationManager.show(
            <?php echo json_encode($errorMessage); ?>,
            'danger',
            'Search Result',
            6000
        );
    });
</script>
<?php endif; ?>

<head>
    <style>
    .danger-notify, .success-notify {
        position: fixed !important;
        top: 1.5rem !important;
        right: 1.5rem !important;
        left: auto !important;
        max-width: 350px;
        width: calc(100vw - 3rem);
        text-align: left;
        z-index: 2000;
        animation: fadeInRight 0.3s ease;
        transition: opacity 0.5s;
        transform: none !important;
    }
    @keyframes fadeInRight {
        from { opacity: 0; right: 0; }
        to { opacity: 1; right: 1.5rem; }
    }
    @media (max-width: 600px) {
        .danger-notify, .success-notify {
            right: 0.5rem !important;
            left: 0.5rem !important;
            max-width: none;
            width: auto;
            font-size: 0.95rem;
        }
    }
    </style>
</head>
<?php require('footer.php'); ?>
<!-- Single Popup Notification Container for search errors -->
<div id="popup-notify" class="danger-notify" style="display:none;"><span id="popup-message"></span></div>
<script>
window.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('popup-notify');
    var popupMsg = document.getElementById('popup-message');
    var msg = '';
    var type = 'danger';
    <?php
    $popup_message = '';
    $popup_type = 'danger';
    if ($errorMessage) {
        $popup_message = $errorMessage;
        $popup_type = 'danger';
    }
    ?>
    msg = <?php echo json_encode($popup_message); ?>;
    type = <?php echo json_encode($popup_type); ?>;
    if (msg && popup && popupMsg) {
        popupMsg.textContent = msg;
        popup.className = type === 'success' ? 'success-notify' : 'danger-notify';
        popup.style.display = 'block';
        popup.style.opacity = '1';
        setTimeout(function() {
            popup.style.opacity = '0';
            setTimeout(function() { popup.style.display = 'none'; }, 1000);
        }, 6000);
    }
});
</script>

<script>
// Enhanced Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchBtn = document.getElementById('searchBtn');
    const locationInput = document.querySelector('input[name="searchLocation"]');
    const roomTypeSelect = document.querySelector('select[name="searchRoomType"]');
    
    // Form validation and enhanced UX
    searchForm.addEventListener('submit', function(e) {
        const location = locationInput.value.trim();
        const roomType = roomTypeSelect.value;
        
        // Validate form
        if (!location && !roomType) {
            e.preventDefault();
            notificationManager.show(
                'Please enter a location or select a room type to search.',
                'warning',
                'Search Required',
                5000
            );
            return false;
        }
        
        // Show loading state
        searchBtn.classList.add('loading');
        searchBtn.disabled = true;
        
        // Add a small delay to show loading state
        setTimeout(() => {
            searchForm.submit();
        }, 500);
    });
    
    // Real-time validation feedback
    function validateSearchInputs() {
        const location = locationInput.value.trim();
        const roomType = roomTypeSelect.value;
        
        if (location || roomType) {
            searchBtn.style.opacity = '1';
            searchBtn.disabled = false;
        } else {
            searchBtn.style.opacity = '0.6';
            searchBtn.disabled = true;
        }
    }
    
    // Add event listeners for real-time validation
    locationInput.addEventListener('input', validateSearchInputs);
    roomTypeSelect.addEventListener('change', validateSearchInputs);
    
    // Initialize validation
    validateSearchInputs();
    
    // Enhanced search suggestions
    const popularLocations = ['Kathmandu', 'Lalitpur', 'Pokhara', 'Chitwan', 'Bhaktapur', 'Dharan'];
    
    locationInput.addEventListener('focus', function() {
        if (!this.value) {
            this.placeholder = 'Start typing location...';
        }
    });
    
    locationInput.addEventListener('blur', function() {
        if (!this.value) {
            this.placeholder = 'Enter Location (e.g., Kathmandu, Lalitpur, Pokhara, Chitwan)';
        }
    });
    
    // Auto-complete functionality
    locationInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        if (value.length > 1) {
            const suggestions = popularLocations.filter(location => 
                location.toLowerCase().includes(value)
            );
            
            // You can implement a dropdown here if needed
            if (suggestions.length > 0 && !suggestions.includes(this.value)) {
                // Show suggestion hint
                this.title = `Suggestions: ${suggestions.join(', ')}`;
            }
        }
    });
    
    // Keyboard navigation
    searchForm.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Mobile optimization
    if (window.innerWidth <= 768) {
        // Auto-focus location input on mobile for better UX
        locationInput.focus();
        
        // Prevent zoom on input focus (iOS)
        locationInput.style.fontSize = '16px';
        roomTypeSelect.style.fontSize = '16px';
    }
    
    // Smooth scroll to search results if they exist
    if (window.location.search.includes('serachRoom')) {
        setTimeout(() => {
            const searchResults = document.getElementById('roomsTitleSearch');
            if (searchResults) {
                searchResults.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }, 500);
    }
    
    // Enhanced error handling for search
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('serachRoom')) {
        // Check if there are any error messages
        const errorElements = document.querySelectorAll('.danger-notify, .warning-notify');
        if (errorElements.length > 0) {
            // Scroll to top to show error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
});

// Enhanced notification for room not found scenarios
function showRoomNotFoundMessage(location, roomType = null) {
    if (roomType) {
        notificationManager.showRoomNotFound(location, roomType);
    } else {
        notificationManager.showRoomNotFound(location);
    }
}

// Search form enhancement for better mobile experience
function enhanceMobileSearch() {
    if (window.innerWidth <= 768) {
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.style.margin = '0 10px';
        }
    }
}

// Call on load and resize
window.addEventListener('load', enhanceMobileSearch);
window.addEventListener('resize', enhanceMobileSearch);
</script>