<?php

require("admin/dbConnect.php");


$createDBQuery ="-- --------------------------------------------------------
-- Host:                         mysql-29620eed-kidssujal-9bd8.j.aivencloud.com
-- Server version:               8.0.35 - Source distribution
-- Server OS:                    Linux
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for rommfinderDB
CREATE DATABASE IF NOT EXISTS `rommfinderDB` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rommfinderDB`;

-- Dumping structure for table rommfinderDB.bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `status` enum('pending','confirmed','canceled') DEFAULT 'pending',
  `is_active` tinyint DEFAULT '1',
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `bookings_ibfk_2` (`room_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.bookings: ~16 rows (approximately)
INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `booking_date`, `description`, `status`, `is_active`) VALUES
	(1, 19, 19, '2025-02-14 18:00:23', 'I want this room urgently , iwill pay more 1000', 'confirmed', 1),
	(3, 23, 20, '2025-02-15 13:01:37', '', 'pending', 1),
	(4, 23, 4, '2025-02-15 13:01:53', '', 'canceled', 1),
	(5, 23, 14, '2025-02-15 13:01:59', '', 'confirmed', 1),
	(6, 23, 10, '2025-02-15 13:28:45', '', 'canceled', 1),
	(7, 23, 18, '2025-02-15 13:28:52', '', 'pending', 1),
	(8, 23, 11, '2025-02-15 13:29:23', 'I want it urgently.', 'pending', 1),
	(9, 23, 6, '2025-02-15 13:29:59', '', 'pending', 1),
	(10, 5, 1, '2025-02-21 03:50:17', 'Is it available?', 'pending', 0),
	(11, 5, 2, '2025-02-21 03:55:13', 'Is room available?', 'canceled', 1),
	(12, 5, 10, '2025-02-23 15:50:55', 'I would like to pay advance payment \r\n', 'confirmed', 1),
	(13, 5, 11, '2025-02-23 15:39:38', '', 'pending', 1),
	(14, 5, 3, '2025-02-23 15:46:06', '', 'pending', 1),
	(15, 5, 9, '2025-02-23 15:46:12', '', 'pending', 1),
	(16, 5, 12, '2025-02-23 15:46:19', '', 'pending', 1),
	(17, 21, 11, '2025-02-23 15:39:38', '', 'pending', 1);

-- Dumping structure for table rommfinderDB.forms
CREATE TABLE IF NOT EXISTS `forms` (
  `id` char(36) NOT NULL DEFAULT (uuid()),
  `form_type` varchar(255) NOT NULL,
  `form_title` varchar(255) NOT NULL,
  `form_data` json NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.forms: ~2 rows (approximately)
INSERT INTO `forms` (`id`, `form_type`, `form_title`, `form_data`, `status`, `updated_at`) VALUES
	('550e8400-e29b-41d4-a716-446655440000', 'Admin', 'Room Form', '{\"formFields\": [{\"name\": \"name\", \"type\": \"text\", \"label\": \"Name\", \"required\": true, \"placeholder\": \"Enter your name\"}, {\"name\": \"email\", \"type\": \"email\", \"label\": \"Email\", \"required\": true, \"placeholder\": \"Enter your email\"}, {\"name\": \"gender\", \"type\": \"select\", \"label\": \"Gender\", \"options\": [\"Male\", \"Female\", \"None\"], \"required\": true, \"placeholder\": \"\"}, {\"name\": \"name\", \"type\": \"email\", \"label\": \"RoomType\", \"required\": true, \"placeholder\": \"\"}]}', 1, '2025-02-26 22:36:16'),
	('550e8400-e29b-41d4-a716-44665544i45o', 'User', 'register_form', '{\"formFields\": [{\"name\": \"user_name\", \"type\": \"text\", \"required\": true, \"validation\": \"min:3|max:70|alpha_spaces\", \"placeholder\": \"Enter your name\"}, {\"name\": \"user_email\", \"type\": \"email\", \"required\": true, \"validation\": \"max:70|email|unique\", \"placeholder\": \"Enter your email\"}, {\"name\": \"user_number\", \"type\": \"number\", \"required\": true, \"validation\": \"digits:10|starts_with:98,97|unique\", \"placeholder\": \"Enter your phone number\"}, {\"name\": \"user_location\", \"type\": \"text\", \"required\": false, \"validation\": \"max:200\", \"placeholder\": \"Enter your location (optional)\"}, {\"name\": \"user_password\", \"type\": \"password\", \"required\": true, \"validation\": \"min:8|contains:uppercase,number,special_character\", \"placeholder\": \"Enter a password\"}, {\"name\": \"user_confirmation_password\", \"type\": \"password\", \"required\": true, \"validation\": \"match:user_password\", \"placeholder\": \"Confirm Password\"}, {\"name\": \"register\", \"type\": \"hidden\", \"value\": \"newRegistration\"}, {\"name\": \"register\", \"text\": \"Register\", \"type\": \"submit\", \"button\": \"submit_button\"}]}', 1, '2025-02-26 17:19:05');

-- Dumping structure for table rommfinderDB.form_fields
CREATE TABLE IF NOT EXISTS `form_fields` (
  `field_id` int NOT NULL AUTO_INCREMENT,
  `field_name` varchar(255) NOT NULL,
  `field_title` varchar(255) DEFAULT NULL,
  `field_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.form_fields: ~23 rows (approximately)
INSERT INTO `form_fields` (`field_id`, `field_name`, `field_title`, `field_status`) VALUES
	(1, 'text', 'Text ', 1),
	(2, 'password', 'Password ', 1),
	(3, 'email', 'Email ', 1),
	(4, 'url', 'URL ', 0),
	(5, 'tel', 'Telephone ', 0),
	(6, 'number', 'Number ', 1),
	(7, 'range', 'Range ', 0),
	(8, 'date', 'Date Picker', 0),
	(9, 'time', 'Time Picker', 0),
	(10, 'datetime-local', 'DateTime Picker', 0),
	(11, 'month', 'Month Picker', 0),
	(12, 'week', 'Week Picker', 0),
	(13, 'checkbox', 'Checkbox', 0),
	(14, 'radio', 'Radio Button', 0),
	(15, 'file', 'File Upload', 0),
	(16, 'hidden', 'Hidden ', 1),
	(17, 'button', 'Button', 0),
	(18, 'submit', 'Submit Button', 0),
	(19, 'reset', 'Reset Button', 0),
	(20, 'image', 'Image Upload', 0),
	(21, 'search', 'Search ', 0),
	(22, 'color', 'Color Picker', 1),
	(23, 'select', 'Select Options', 1);

-- Dumping structure for table rommfinderDB.form_managers
CREATE TABLE IF NOT EXISTS `form_managers` (
  `form_id` char(36) NOT NULL DEFAULT (uuid()),
  `form_name` varchar(255) NOT NULL,
  `form_slug` varchar(255) NOT NULL,
  `description` text,
  `field_detail` json DEFAULT NULL,
  `background_color` varchar(27) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`form_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `form_managers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `form_managers_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.form_managers: ~1 rows (approximately)
INSERT INTO `form_managers` (`form_id`, `form_name`, `form_slug`, `description`, `field_detail`, `background_color`, `background_image`, `created_at`, `updated_at`, `status`, `created_by`, `updated_by`) VALUES
	('67c1d98b2ec16', 'Administrator Dashboard Login', 'login_admin', 'Welcome to the RoomFinder Admin Panel. This secure portal allows administrators to manage room listings, monitor user activity, and ensure seamless operations within the platform. Log in to access advanced tools for managing rooms, handling user inquiries, and controlling app settings.', '[{\"name\": \"userEmailByLogin\", \"type\": \"text\", \"label\": \"Email\", \"required\": true, \"placeholder\": \"Enter your Email\"}, {\"name\": \"userPasswordByLogin\", \"type\": \"password\", \"label\": \"Password\", \"required\": true, \"placeholder\": \"Enter your password\"}]', '#f8fc03', 'uploads/67c34612d0c91_gAdani.png', '2025-02-28 16:49:50', '2025-03-01 18:40:07', 1, NULL, NULL);

-- Dumping structure for table rommfinderDB.media
CREATE TABLE IF NOT EXISTS `media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.media: ~19 rows (approximately)
INSERT INTO `media` (`id`, `image_name`, `image_path`, `status`, `created_at`) VALUES
	(52, 'HRP_Room1.jpg', 'uploads/67a9fe65878c1_HRP_Room1.jpg', 1, '2025-02-10 00:00:00'),
	(55, 'traditional newari.jpg', 'uploads/67aa00fb4ad70_traditional newari.jpg', 0, '2025-02-10 00:00:00'),
	(58, 'sps-lp-services-desktop-interior-paint.jpg', 'uploads/67aa016f2dcae_sps-lp-services-desktop-interior-paint.jpg', 0, '2025-02-10 00:00:00'),
	(59, '21 tara.jpg', 'uploads/67aa028646942_21 tara.jpg', 1, '2025-02-10 14:43:34'),
	(62, 'Image20250108215443.jpg', 'uploads/67aa02f1e5984_Image20250108215443.jpg', 0, '2025-02-10 14:45:21'),
	(68, 'backgroundLogin.png', 'uploads/67ae0a34aac00_backgroundLogin.png', 0, '2025-02-13 16:05:24'),
	(69, 'resort room.png', 'uploads/67ae111c67cc0_resort room.png', 1, '2025-02-13 16:34:52'),
	(70, '1bhknew.png', 'uploads/67ae111c84edb_1bhknew.png', 1, '2025-02-13 16:34:52'),
	(71, '2bhkfullyFurnished.png', 'uploads/67ae111ca0ea5_2bhkfullyFurnished.png', 1, '2025-02-13 16:34:52'),
	(72, '2BHKRiver.png', 'uploads/67ae111cbce52_2BHKRiver.png', 1, '2025-02-13 16:34:52'),
	(73, '2BHK.png', 'uploads/67ae111cd95af_2BHK.png', 1, '2025-02-13 16:34:52'),
	(74, '1bhkroom.png', 'uploads/67ae111d0164f_1bhkroom.png', 1, '2025-02-13 16:34:53'),
	(76, 'resort room.png', 'uploads/67ae128f24dc0_resort room.png', 1, '2025-02-13 16:41:03'),
	(77, 'New-Furnished-Apartment.png', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', 1, '2025-02-13 16:45:22'),
	(78, '8c70c328f1fa11ec88a20a58a9feac02.png', 'uploads/67ae1392f0187_8c70c328f1fa11ec88a20a58a9feac02.png', 1, '2025-02-13 16:45:22'),
	(79, 'd5cbce1f01e8084d70e446112fba660c1bhk.png', 'uploads/67ae20c863f70_d5cbce1f01e8084d70e446112fba660c1bhk.png', 0, '2025-02-13 17:41:44'),
	(80, '9d68999a6f0b33b5b47fee6b030026d2single.png', 'uploads/67ae20c891e7f_9d68999a6f0b33b5b47fee6b030026d2single.png', 0, '2025-02-13 17:41:44'),
	(81, 'f9a15809c7cae001c72c19450f363769bhk.png', 'uploads/67ae20c8bc201_f9a15809c7cae001c72c19450f363769bhk.png', 1, '2025-02-13 17:41:44'),
	(82, 'IndianAve-134-af033b5334674735acdb36cd6498a7f6.jpg', 'uploads/67ae40340153c_IndianAve-134-af033b5334674735acdb36cd6498a7f6.jpg', 0, '2025-02-13 19:55:48'),
	(84, '360_F_399307378_LuSoCJrfkRn2jAGaByqAi771EF1QTwdf.jpg', 'uploads/67c1d87658971_360_F_399307378_LuSoCJrfkRn2jAGaByqAi771EF1QTwdf.jpg', 0, '2025-02-28 16:38:30'),
	(85, 'Room Finder Nepal.png', 'uploads/67c1e540a8427_Room Finder Nepal.png', 0, '2025-02-28 17:33:04'),
	(86, 'homeLogoRM.png', 'uploads/67c2ff389f8be_homeLogoRM.png', 0, '2025-03-01 13:36:08'),
	(87, 'Harshat-Mehta1.jpg', 'uploads/67c312f48c919_Harshat-Mehta1.jpg', 0, '2025-03-01 15:00:20'),
	(88, 'Benefit Calculator Form.jpg', 'uploads/67c3459a6682f_Benefit Calculator Form.jpg', 0, '2025-03-01 18:36:26'),
	(89, 'Visa_Debit_Card.png', 'uploads/67c345d4c36c1_Visa_Debit_Card.png', 0, '2025-03-01 18:37:24'),
	(90, 'gAdani.png', 'uploads/67c34612d0c91_gAdani.png', 0, '2025-03-01 18:38:26');

-- Dumping structure for table rommfinderDB.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) NOT NULL,
  `room_location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `room_price` int unsigned DEFAULT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `room_status` enum('active','inActive') NOT NULL,
  `room_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `room_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.rooms: ~20 rows (approximately)
INSERT INTO `rooms` (`room_id`, `room_name`, `room_location`, `room_price`, `room_type`, `room_status`, `room_description`, `room_image`, `created_at`) VALUES
	(1, 'Deluxe Suite', 'New Baneshwor, Kathmandu', 15000, 'Deluxe', 'active', 'A luxurious and spacious suite with a king-sized bed, private balcony, and high-end amenities to make your stay unforgettable. Perfect for couples or business travelers seeking comfort and style.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(2, 'Standard Single Room', 'Jawlhakhel, Lalitpur', 50, '1BHK', 'active', 'A comfortable and well-furnished single room designed for solo travelers. Includes a single bed, a work desk, and all essential amenities for a relaxing stay.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(3, 'Family Suite', 'Jawlakhel, Lalitpur', 11200, 'Suite', 'active', 'A spacious family suite featuring two separate bedrooms, a large living area, and family-friendly amenities. Ideal for families or small groups looking for comfort and convenience.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(4, 'Executive Room', 'Thimi, Bhaktapur', 10200, '1BHK', 'active', 'An executive room with a work desk, comfortable seating, and modern amenities, perfect for business travelers who need to balance work and relaxation in style.', 'uploads/67aa02f1e5984_Image20250108215443.jpg', '2025-02-12 17:03:38'),
	(5, 'Superior Room', 'Patan, Lalitpur', 18085, '1BHK', 'active', 'A superior room offering a perfect blend of comfort and elegance. Features include upgraded amenities, a cozy atmosphere, and a beautiful view of the surrounding area.', 'uploads/67ae128f24dc0_resort room.png', '2025-02-12 17:03:38'),
	(6, 'Newari Traditonal House For Rent', 'Kamalbinayak, Bhaktapur', 29500, 'Apartment', 'active', 'A lavish tradtitional style rooms with an expansive living space, private pool, jacuzzi, and panoramic views of Bhaktapur. The epitome of luxury and sophistication.', 'uploads/67aa00fb4ad70_traditional newari.jpg', '2025-02-12 17:03:38'),
	(7, 'Double Room', 'Lagankhel, Lalitpur', 75, 'Double', 'active', 'A well-appointed double room with two comfortable beds, ideal for friends or couples. Enjoy modern furnishings, a cozy atmosphere, and all the essential amenities for a pleasant stay.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(8, 'King Room', 'Balkumari, Lalitpur', 19990, 'King', 'active', 'A luxurious king room with a spacious layout, featuring a king-sized bed, plush furnishings, and top-notch amenities. Perfect for those seeking a relaxing retreat in a quiet location.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(9, 'Twin Room', 'Patan Durbar Square, Lalitpur', 16665, 'Twin', 'active', 'A charming twin room offering two single beds and a serene ambiance, ideal for friends or family members who want to stay close yet maintain their privacy.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(10, 'Luxury Suite', 'Patan, Lalitpur', 180, 'Suite', 'inActive', 'A grand luxury suite with a spacious living area, plush bedding, and modern amenities. Perfect for guests looking for an indulgent stay with all the comforts of home and more.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(11, 'Garden View Room', 'Lalitpur, Lalitpur', 39000, '2BHK', 'active', 'A peaceful room with a beautiful view of a well-maintained garden. This room offers a relaxing environment, ideal for guests who enjoy a bit of nature during their stay.', 'uploads/67ae111cbce52_2BHKRiver.png', '2025-02-12 17:03:38'),
	(12, 'New Fully Furnished Apartment', 'Buddhanilkantha, Kathmandu', 500, 'apartment', 'active', 'A luxurious apartment with stunning panoramic views of the Kathmandu Valley and the Himalayas. Equipped with high-end facilities, it’s perfect for guests looking for ultimate privacy and comfort.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(13, 'Budget Room', 'Kalanki, Kathmandu', 4000, 'Budget', 'active', 'A cozy budget-friendly room offering essential amenities for guests who are looking for affordable accommodation without compromising on comfort.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(14, 'Poolside Apartment', 'Khumaltar, Lalitpur', 39905, 'apartment', 'inActive', 'A stylish appartment with a direct view of the swimming pool, featuring comfortable furnishings and a peaceful atmosphere. Perfect for guests who enjoy a refreshing swim during their stay.', 'uploads/67ae111cbce52_2BHKRiver.png', '2025-02-12 17:03:38'),
	(15, 'Studio Room', 'Thamel, Kathmandu', 80000, 'Studio', 'active', 'A compact yet stylish studio room with modern amenities, ideal for short stays or solo travelers looking for a convenient, all-in-one living space.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(16, 'Honeymoon Suite', 'Sundhara, Kathmandu', 20000, 'Suite', 'active', 'A romantic honeymoon suite designed for couples, with a large king-sized bed, a jacuzzi, and intimate lighting. A perfect retreat for newlyweds or couples seeking a luxurious getaway.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(18, 'Luxury Twin Room', 'Maitidevi, Kathmandu', 39500, '2BHK', 'active', 'A luxury twin room with two comfortable beds, spacious layout, and stylish furnishings. Ideal for guests who enjoy a high-end experience and great value for money.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38'),
	(19, 'Business Room', 'Baneswor, Kathmandu', 18500, '2BHK', 'inActive', 'A room designed for business travelers, with a work desk, fast internet connection, and all the amenities needed for a productive stay in a quiet and convenient location.', 'uploads/67ae111ca0ea5_2bhkfullyFurnished.png', '2025-02-12 17:03:38'),
	(20, 'Cottage Room', 'Chobhar, Kathmandu', 6000, '1BHK', 'inActive', 'A charming room situated in a peaceful cottage setting, offering privacy and a connection with nature. Ideal for guests who want a more tranquil and rustic experience.', 'uploads/67ae1392c5b2a_New-Furnished-Apartment.png', '2025-02-12 17:03:38');

-- Dumping structure for table rommfinderDB.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_number` bigint unsigned NOT NULL,
  `user_location` varchar(255) DEFAULT NULL,
  `user_status` enum('active','inActive') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'inActive',
  `user_type` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rommfinderDB.users: ~14 rows (approximately)
INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_number`, `user_location`, `user_status`, `user_type`) VALUES
	(1, 'Super Admin', 'super@admin.com', '$2y$10$8pA58FZ5433EiLlAqnEE0O1Aae3EW3sKlpxnpX/J.1d4P6BgNcKwi', 9821234561, 'New LionPark', 'active', 'admin'),
	(2, 'Admin/*//', 'xujivyl@mailitor.com', '$2y$10$/krGBi.HvG2R7bR1FzwG8e11J/wWIuxWFWsVUETjCsJivIiKeg.Ya', 9812345600, 'Anim blanditiis cons', 'inActive', 'user'),
	(3, 'User One(Maeth)', 'userone@yopmail.com', '$2y$10$UxDk9iBPVW7uPKMCh9pDYO9SAPfO0CAQ6vtV5SbXv/uJIoj2RUDPi', 9851415675, 'FM Bazar,NewRoad', 'inActive', 'user'),
	(4, 'Dr. Board', 'noord@gmail.com', '$2y$10$tjExuUDc5KcD0wXHQEbuxufKPnSxit4YA0stcSlumlvJWV5iAf0Ga', 9807020231, 'Bhaktapur,Nepal', 'active', 'user'),
	(5, 'Nabin Ojha', 'nabin@gmail.com', '$2y$10$JjWLxxbjr0/wWnxDaT/aBOpff4Y5M7JQWiDv5acBnpPzYbtK/7ONi', 9843125000, 'New Baneshwor', 'active', 'user'),
	(6, 'Ravi Das', 'ravidas846@gmail.com', '$2y$10$TYL1zdrbI1aiSju0RMQQQeVFTt8EP41kNJUGIygOFahsS3kRYVVi6', 9843959440, 'Sinamangal,Kathmandu', 'inActive', 'user'),
	(7, 'sunil', 'sunilthakur4656@gmail.com', '$2y$10$VMAiCJENRkOJTtNa8GIcgORcM.45qZu8/zeXw2iXD2YdnJyCLjVny', 9819724656, 'Pepsicola, Bhaktapur', 'inActive', 'user'),
	(8, 'sunil', 'sunil123@gmail.com', '$2y$10$s9FS9jWneuUrEwrcP4sFR.g35xlSLXvs6lfKl6uq8F9QBOh441Kwe', 9819724656, 'Maitighar, Kathmandu', 'inActive', 'user'),
	(15, 'Dur e Fishan', 'dur@gmail.com', '$2y$10$Vxi0SyHhPpOO.WHyhjuwieq/9llUgZN1nNQqT2VK4G2Tq0KXh8ek6', 9807020258, 'Bhaktapur,Nepal', 'inActive', 'user'),
	(18, 'NewMew ', 'newmew@gmail.com', '$2y$10$U4hJLPOwaOeRvOuPUMMMce/ZOp9toR9go2LsfNS4/lJZ5bkvjcPay', 9874561230, 'Lazimpat,Kathmandu', 'inActive', 'user'),
	(19, 'BCA Demo', 'bca@gmail.com', '$2y$10$isdORJ8f6JtnpCZNqXCt1.i6sto6u7rHxkvXHoL2DAgdRwT/vmxHa', 9817020231, 'New Baneshwor', 'active', 'user'),
	(20, 'Manju Shree', 'Snajay@yopmail.com', '$2y$10$kcA/JpCBojSn5EimK4xIxeHgAvaMkcxQ.6UW.avWxwvtTjCYG4huu', 9807020256, 'New Baneshwor', 'active', 'user'),
	(21, 'Dhiraj Shakya', 'dhiajAnup@gmail.com', '$2y$10$eMLoBaDAwKwYKcDXzrYY.umasEvcnimetXFlXf9oqgBvhVD8kU/fm', 9854785236, 'Shantinagar ,Kathmandu', 'inActive', 'user'),
	(22, 'gudic', 'nivorijag@mailinator.com', '$2y$10$nvsvQC7v2shj/aEjbXu9Ee8Lv6WYaIx/3i4TLkRrNeXw3Lp4/xPhm', 9845210559, 'Et mollitia duis sed', 'inActive', 'user'),
	(23, 'Prem Bhandhari', 'prem@gmail.com', '$2y$10$WasjoTWZR8YzfX9E8PpJ6ecsnLhikqMiNc4wvD9512Avyf5/xxiqC', 9866865474, 'New Bansehwor 03, KTM', 'active', 'user'),
	(24, 'Sujal Awal', 'sujal@gmail.com', '$2y$10$CLLlfir0UfC2IJol0LCVpucR5f.u4sWlcJPVk.wVVnz9nCOGLt.CG', 9871236540, 'Bhaktapur', 'inActive', 'user');
 ";

if($conn->multi_query($createDBQuery)){
echo "Data inserted successfully.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();