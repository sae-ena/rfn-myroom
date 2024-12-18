CREATE TABLE facilities (
    facility_id INT AUTO_INCREMENT PRIMARY KEY,
    facility_name VARCHAR(255) NOT NULL
);

CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(255) NOT NULL,
    room_location VARCHAR(255) NOT NULL,
    room_price INT UNSIGNED,
    room_type VARCHAR(255),
    Status Enum('active','inActive') NOT NULL,
);