<?php
// Migration: Create backend_settings table
// Usage: php migrations/20240610_create_backend_settings.php

require_once __DIR__ . '/../admin/dbConnect.php';

$sql = "CREATE TABLE IF NOT EXISTS `backend_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `value` TEXT,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Migration successful: backend_settings table created or already exists.\n";
} else {
    echo "Migration failed: " . $conn->error . "\n";
}
$conn->close(); 