-- --------------------------------------------------------
-- Host:                         mysql-29620eed-kidssujal-9bd8.j.aivencloud.com
-- Server version:               8.0.30 - Source distribution
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


-- Dumping database structure for rf_db
DROP DATABASE IF EXISTS `rf_db`;
CREATE DATABASE IF NOT EXISTS `rf_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rf_db`;

-- Dumping structure for table rf_db.bookings
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','canceled') DEFAULT 'pending',
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rf_db.bookings: ~3 rows (approximately)
DELETE FROM `bookings`;
INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `booking_date`, `description`, `status`) VALUES
	(1, 3, 20, '2024-12-21 00:46:58', 'We want it urgently . We will pay as you want.', 'pending'),
	(2, 2, 20, '2024-12-01 00:48:45', 'Can we have conversatin to negotiate', 'canceled'),
	(3, 1, 19, '2024-12-21 02:40:54', 'I am testing the page ', 'pending');

-- Dumping structure for table rf_db.rooms
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) NOT NULL,
  `room_location` varchar(255) NOT NULL,
  `room_price` int unsigned DEFAULT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `room_status` enum('active','inActive') NOT NULL,
  `room_description` varchar(255) DEFAULT NULL,
  `room_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rf_db.rooms: ~18 rows (approximately)
DELETE FROM `rooms`;
INSERT INTO `rooms` (`room_id`, `room_name`, `room_location`, `room_price`, `room_type`, `room_status`, `room_description`, `room_image`, `created_at`) VALUES
	(5, 'Suite Room', 'Bhaktapur', 15000, '1BHK', 'inActive', 'A lavish suite with a living area, balcony, and historical city views from the heart of Bhaktapur.', '', NULL),
	(6, 'Luxury Room', 'Nagarkot', 12000, 'Luxury', 'inActive', 'A luxury room with breathtaking views of the Himalayas and full amenities for a peaceful retreat.', 'images/luxury_room_nagarkot.jpg', NULL),
	(7, 'Honeymoon Suite', 'Pokhara', 10000, 'Suite', 'inActive', 'A romantic suite with a private balcony overlooking the lake, ideal for honeymooners.', 'images/honeymoon_suite_pokhara.jpg', NULL),
	(8, 'Business Room', 'Kathmandu', 7000, 'Business', 'inActive', 'A modern room with a workstation, high-speed internet, and comfortable facilities for business travelers.', 'images/business_room_kathmandu.jpg', NULL),
	(9, 'Cottage Room', 'Bandipur', 6000, 'Cottage', 'active', 'A cozy cottage room in a traditional hilltop village, surrounded by nature and tranquility.', 'images/cottage_room_bandipur.jpg', NULL),
	(10, 'Penthouse Suite', 'Thamel, Kathmandu', 25000, 'Shared Room', 'active', 'An exclusive penthouse suite with panoramic views of Kathmandu valley and luxurious amenities.', '67630b72ab163_jGandhi.png', NULL),
	(11, '', 'Lumbini', 5000, '1BHK', 'inActive', 'A peaceful room with a beautiful view of the garden and proximity to the Lumbini Monastery.', '', NULL),
	(12, 'Riverside Room', 'poppon', 4500, 'Single Room', 'active', 'A serene riverside room with a tranquil atmosphere, perfect for nature lovers.', '', NULL),
	(13, 'Anim corporis eum pe', 'Officiis dolor cum s', 875, 'Entire Apartment', 'active', 'Sint qui quia optio', '67631473a787e_Screenshot (17).png', NULL),
	(14, 'Eum eum velit cillu', 'Voluptates et eaque ', 889, 'Studio', 'inActive', 'Aut praesentium veri', '6761e09c748e2_mine.png', NULL),
	(15, 'Optio sed ipsum al', 'Quis pariatur Qui n', 264, 'Studio', 'inActive', 'Nostrum distinctio ', '6763133a5a616_gAdani.png', NULL),
	(16, 'Omnis dolore et magn', 'Nihil magni amet do', 794, '1BHK', 'inActive', 'Omnis ut obcaecati n', '6761de643275b_dEFishan.png', NULL),
	(17, 'Est possimus optio', 'Saepe et iure quis s', 705, 'Single Room', 'active', 'Nemo eos est eaque ', '67631b044054e_Screenshot_20241124_151954_LinkedIn.jpg', NULL),
	(18, 'Et odio vel et asper', 'Deserunt aperiam sit', 85, '1BHK', 'inActive', 'Provident quia libe', '', NULL),
	(19, 'Exercitation quaerat', 'Minima sint veniam ', 76, 'Studio', 'inActive', 'Facere ad incididunt', '67631231d5999_passportZebra.jpg', NULL),
	(20, 'New Smart Room', 'kathmandu,Lazimpath', 169000, 'Entire Apartment', 'inActive', 'At Destination visit and I will send you a little more time for the eaque quia volup', '6765b7627ca83_gAdani.png', NULL),
	(21, 'New Fully Furnished ', 'Jhamsikhel,Kathmandu', 29000, '2BHK', 'active', 'No matter if you call it a living room, family room, den, or even keeping room, you\\\'ve got that one room in your home, aside from the kitchen, that\\\'s intended for both family and company. ', '67684108e8fbe_IndianAve-134-af033b5334674735acdb36cd6498a7f6.jpg', NULL),
	(22, 'Urgent- Room ForRent', 'Fulpo,Jalpha', 9999, 'Single Room', 'active', '\\"Sometimes, bamboo and rattan with blues can feel kind of sweet,” notes Hannon Doody, who painted her Lookout Mountain, Tennesee, family room’s windows, doors, and oversize mantel black to “create a little edge.', '676841532cfa7_Copyof27498_LEB13159F-1-1-c09eb55cd56c4610aaa5c8a1218ed408.jpg', NULL);

-- Dumping structure for table rf_db.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_number` bigint unsigned NOT NULL,
  `user_location` varchar(255) DEFAULT NULL,
  `user_status` enum('active','inActive') NOT NULL,
  `user_type` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rf_db.users: ~3 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_number`, `user_location`, `user_status`, `user_type`) VALUES
	(1, 'Super Admin', 'xevyqacaro@mailinator.com', '$2y$10$8pA58FZ5433EiLlAqnEE0O1Aae3EW3sKlpxnpX/J.1d4P6BgNcKwi', 982123456, 'New LionPark', 'active', 'admin'),
	(2, 'Admin/*//', 'xujivyl@mailitor.com', '$2y$10$/krGBi.HvG2R7bR1FzwG8e11J/wWIuxWFWsVUETjCsJivIiKeg.Ya', 123456, 'Anim blanditiis cons', 'active', 'user'),
	(3, 'User One(Maeth)', 'userone@yopmail.com', '$2y$10$UxDk9iBPVW7uPKMCh9pDYO9SAPfO0CAQ6vtV5SbXv/uJIoj2RUDPi', 1415675, 'FM Bazar,NewRoad', 'inActive', 'user');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
