-- SQL updates for registration system
-- Add these columns to existing tables

-- Update tblstudent table
ALTER TABLE `tblstudent` 
ADD COLUMN `password` VARCHAR(255) NULL AFTER `contact_number`,
ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Update tblinstructor table  
ALTER TABLE `tblinstructor`
ADD COLUMN `employee_id` VARCHAR(50) NULL AFTER `instructor_id`,
ADD COLUMN `position` VARCHAR(100) DEFAULT 'Instructor',
ADD COLUMN `specialization` VARCHAR(255) NULL,
ADD COLUMN `password` VARCHAR(255) NULL,
ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create tbladmin table for admin users
CREATE TABLE IF NOT EXISTS `tbladmin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `dept_id` int(11) NULL,
  `access_level` varchar(50) NOT NULL DEFAULT 'System Admin',
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `dept_id` (`dept_id`),
  FOREIGN KEY (`dept_id`) REFERENCES `tbldepartment` (`dept_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create tbluser_sessions for login tracking
CREATE TABLE IF NOT EXISTS `tbluser_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_type` enum('student','faculty','admin') NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`session_id`),
  KEY `user_lookup` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
ALTER TABLE `tblstudent` ADD INDEX `idx_email` (`email`);
ALTER TABLE `tblinstructor` ADD INDEX `idx_email` (`email`);
ALTER TABLE `tblinstructor` ADD INDEX `idx_employee_id` (`employee_id`);

-- Insert sample admin user (password: admin123)
INSERT INTO `tbladmin` (`employee_id`, `first_name`, `last_name`, `email`, `access_level`, `password`) 
VALUES ('ADM-2024-001', 'System', 'Administrator', 'admin@pup.edu.ph', 'Super Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;