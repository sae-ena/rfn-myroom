<?php
$host = 'mysql-29620eed-kidssujal-9bd8.j.aivencloud.com'; // Database host
$username = 'avnadmin'; // Database username
$password = 'AVNS_1N9Dr_M5lJZIRxcd8gj'; // Database password
$dbname = 'rf_db'; // Database name
$portNo = 18250;

// Create connection
$conn = new mysqli($host, $username, $password, $dbname,$portNo);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Step 1 :CREATE DATABASE rf_db;
/*
Step 2 :CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(255) NOT NULL,
    room_location VARCHAR(255) NOT NULL,
    room_price INT UNSIGNED,
    room_type VARCHAR(255),
    room_status ENUM('active', 'inActive') NOT NULL,
    room_description VARCHAR(255),
    room_image VARCHAR(255)
);
*/
/*
-- Room 1: Deluxe Room in Kathmandu
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Deluxe Room', 'Kathmandu', 8000, 'Deluxe', 'active', 
    'A luxurious deluxe room with a king-size bed, air-conditioning, and panoramic city views.', 
    'images/deluxe_room_kathmandu.jpg');

-- Room 2: Standard Room in Pokhara
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Standard Room', 'Pokhara', 4000, 'Standard', 'active', 
    'A comfortable standard room with mountain views, a cozy bed, and a peaceful environment.', 
    'images/standard_room_pokhara.jpg');

-- Room 3: Family Room in Lalitpur
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Family Room', 'Lalitpur', 10000, 'Family', 'active', 
    'A spacious family room with multiple beds, perfect for group stays with modern amenities.', 
    'images/family_room_lalitpur.jpg');

-- Room 4: Economy Room in Chitwan
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Economy Room', 'Chitwan', 3000, 'Economy', 'active', 
    'An affordable room for budget travelers, located near Chitwan National Park.', 
    'images/economy_room_chitwan.jpg');

-- Room 5: Suite Room in Bhaktapur
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Suite Room', 'Bhaktapur', 15000, 'Suite', 'active', 
    'A lavish suite with a living area, balcony, and historical city views from the heart of Bhaktapur.', 
    'images/suite_room_bhaktapur.jpg');

-- Room 6: Luxury Room in Nagarkot
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Luxury Room', 'Nagarkot', 12000, 'Luxury', 'active', 
    'A luxury room with breathtaking views of the Himalayas and full amenities for a peaceful retreat.', 
    'images/luxury_room_nagarkot.jpg');

-- Room 7: Honeymoon Suite in Pokhara
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Honeymoon Suite', 'Pokhara', 10000, 'Suite', 'active', 
    'A romantic suite with a private balcony overlooking the lake, ideal for honeymooners.', 
    'images/honeymoon_suite_pokhara.jpg');

-- Room 8: Business Room in Kathmandu
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Business Room', 'Kathmandu', 7000, 'Business', 'active', 
    'A modern room with a workstation, high-speed internet, and comfortable facilities for business travelers.', 
    'images/business_room_kathmandu.jpg');

-- Room 9: Cottage Room in Bandipur
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Cottage Room', 'Bandipur', 6000, 'Cottage', 'active', 
    'A cozy cottage room in a traditional hilltop village, surrounded by nature and tranquility.', 
    'images/cottage_room_bandipur.jpg');

-- Room 10: Penthouse Suite in Thamel, Kathmandu
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Penthouse Suite', 'Thamel, Kathmandu', 25000, 'Penthouse', 'active', 
    'An exclusive penthouse suite with panoramic views of Kathmandu valley and luxurious amenities.', 
    'images/penthouse_suite_thamel.jpg');

-- Room 11: Garden View Room in Lumbini
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Garden View Room', 'Lumbini', 5000, 'Standard', 'active', 
    'A peaceful room with a beautiful view of the garden and proximity to the Lumbini Monastery.', 
    'images/garden_view_room_lumbini.jpg');

-- Room 12: Riverside Room in Ilam
INSERT INTO rooms (
    room_name, room_location, room_price, room_type, 
    room_status, room_description, room_image
) 
VALUES 
    ('Riverside Room', 'Ilam', 4500, 'Standard', 'active', 
    'A serene riverside room with a tranquil atmosphere, perfect for nature lovers.', 
    'images/riverside_room_ilam.jpg');

*/
/* Step 3 :
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_number INT UNSIGNED,
    user_location VARCHAR(255) NULL,
    user_status ENUM('active', 'inActive') NOT NULL
);
*/

// $roomTableCheckingQuery = "SELECT 1 FROM `rooms` LIMIT 1;";
// $roomTableCheckingResult =$conn->query($roomTableCheckingQuery);

// if(! $roomTableCheckingResult->field_count >0 ){
//     $createRoom="CREATE TABLE rooms (
//         room_id INT AUTO_INCREMENT PRIMARY KEY,
//         room_name VARCHAR(255) NOT NULL,
//         room_location VARCHAR(255) NOT NULL,
//         room_price INT UNSIGNED,
//         room_type VARCHAR(255),
//         room_status ENUM('active','inActive') NOT NULL
//     );";
//     if ($conn->query($createRoom) === TRUE) {
//     echo "Table 'rooms' created successfully.";
// } else {
//     echo "Error creating table: " . $conn->error;
// }
// }
// $tableCheckQuery =  "DESCRIBE `users`;";
// $tableCheckResult = $conn->query($tableCheckQuery);
// // Check if the table exists
// if ($tableCheckResult === false) {
//     $createUser="CREATE TABLE users (
//         user_id INT AUTO_INCREMENT PRIMARY KEY,
//         user_name VARCHAR(255) NOT NULL,
//         user_email VARCHAR(255) NOT NULL,
        // user_password VARCHAR(255) NOT NULL,
//         user_number INT UNSIGNED,
//         user_location VARCHAR(255) NULL,
//         user_status ENUM('active','inActive') NOT NULL
//     );";
//     if ($conn->query($createUser) === TRUE) {
//     echo "Table 'users' created successfully.";
// } 
// }
// $conn->close();

?>