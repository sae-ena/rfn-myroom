
<?php require('header.php') ; ?>
        <!-- Hero Section Start -->
        <section class="hero">
          <div class="hero-content">
            <h1>Find Your Perfect Place</h1>
            <p>Search for locations and room types that suit your needs.</p>
            
            <div class="search-form">
              <input type="text" placeholder="Enter Location" class="location-input" />
              <select class="room-type-input">
                <option value="single">Single Room</option>
                <option value="double">Double Room</option>
                <option value="suite">Suite</option>
                <option value="apartment">Apartment</option>
              </select>
              <button class="search-btn">Search</button>
            </div>
          </div>
        </section>
      

    
      <div class="container-aboutUs" id="about">
        <div class="aboutSectionLeft">
          <h2 class="fs-2">About Us</h2>

          <p>
            Welcome to Casabo Romm Finder, nestled in the heart of Kathmandu, Nepal. We are a premium destination offering a unique blend of luxury and comfort, where traditional Nepalese hospitality meets modern elegance. Whether you’re visiting for a relaxing getaway, a family retreat, or a corporate escape, our resort promises an unforgettable experience. Surrounded by breathtaking landscapes and steeped in rich cultural heritage, Casabo Resort is the perfect place to unwind and explore the beauty of Nepal. Come experience our exceptional services and immerse yourself in the tranquility and luxury of our beautifully designed rooms, world-class amenities, and exclusive experiences.
          </p>
        </div>

        <div class="imageSection">
         
        </div>
      </div>
    

    <div class="interior-info-section">
      <div class="title">
        <h4 class="text-center fs-2 fw-bolder">Our Interior</h4>
      </div>
      <div
        class="container-md container-fluid-lg d-flex justify-content-around mt-4 gap-5"
      >
        <div class="col-5">
          <img src="images/interrioroom.jpg" alt="" width="100%" />
        </div>
        <div class="col-6">
          <div class="texts">
            <h2>Rooms</h2>
            <p class="fs-5">
              Dealers can list available rooms with detailed information, including room type, price, amenities, location, and high-quality images. Each listing is designed to provide all necessary details to help users make informed decisions.
            </p>

            <h2>Amenities</h2>
            <ul class="fs-5">
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
            <div class="text-center mt-4">
              <button class="btn btn-warning ml-5 fs-6">Find Out More</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="gallery_section p-5" id="gallery">
      <div class="title mt-4">
        <h4 class="text-center fs-2 fw-bolder">Gallery</h4>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-5">
            <img
              src="images/Gallery1.jpg"
              width="100%"
              height="50%"
              srcset=""
            />
          </div>

          <div class="col-md-7">
            <div class="row container-fluid">
              <div class="col-sm-6 col-md-6 mt-4">
                <img src="images/Gallery2.jpg" alt="" width="100%" />
              </div>
              <div class="col-sm-6 col-md-6 mt-4">
                <img src="images/Gallery3.jpg" alt="" width="100%" />
              </div>
            </div>
            <div class="row container-fluid">
              <div class="col-md-6 col-sm-6 mt-4">
                <img src="images/Gallery5.jpg" alt="" width="100%" />
              </div>
              <div class="col-md-6 col-sm-6 mt-4">
                <img src="images/Gallery4.jpg" alt="" width="100%" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <h4 class="roomsTitle" id="roomsTitle">Explore Rooms 
    <?php if(! isset($_SESSION['auth_id'])){  
    echo'<span style="position:relative;left:691px; font-size:16px;padding: 10px 30px;background-color:rgb(219, 57, 57); border-radius:36px 12px">Please log in to book a room.</span>';} ?></h4>
    <div class="Rooms">
     
      <?php require('roomData.php'); ?>
    </div>

    
    <?php require('footer.php') ; ?>