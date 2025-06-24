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

CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_title VARCHAR(255) NOT NULL,
    user_message TEXT NOT NULL,
    admin_mail VARCHAR(255) DEFAULT NULL,
    admin_message TEXT DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    template_variables TEXT DEFAULT NULL, -- JSON or comma-separated list of variable names
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- You can add more fields as needed for your CMS
