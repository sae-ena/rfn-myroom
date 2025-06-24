<?php
// Migration: Create email_templates table
// Usage: php migrations/20240609_create_email_templates.php

require_once __DIR__ . '/../admin/dbConnect.php';

$sql = "CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_title` VARCHAR(255) NOT NULL,
  `user_message` TEXT NOT NULL,
  `admin_mail` VARCHAR(255),
  `admin_message` TEXT,
  `status` TINYINT(1) DEFAULT 1,
  `template_variables` VARCHAR(255),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Migration successful: email_templates table created or already exists.\n";
} else {
    echo "Migration failed: " . $conn->error . "\n";
}
$conn->close(); 