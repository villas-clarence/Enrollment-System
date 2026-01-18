-- Setup login credentials for all existing students
-- This will allow all 124 students to login using their student_no as username

-- First, add password column to tblstudent if not exists
ALTER TABLE `tblstudent` 
ADD COLUMN IF NOT EXISTS `password` VARCHAR(255) NULL AFTER `program_id`,
ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Set default password for all existing students
-- Password will be "student123" (hashed)
UPDATE `tblstudent` 
SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE `password` IS NULL OR `password` = '';

-- Alternative: Set password as their student number (uncomment if preferred)
-- UPDATE `tblstudent` 
-- SET `password` = CONCAT('$2y$10$', MD5(CONCAT('student_', `student_no`)))
-- WHERE `password` IS NULL OR `password` = '';

-- Create index for faster login queries
ALTER TABLE `tblstudent` ADD INDEX IF NOT EXISTS `idx_student_login` (`student_no`, `password`);

-- Verify the update
SELECT 
    student_id,
    student_no,
    CONCAT(first_name, ' ', last_name) as full_name,
    email,
    year_level,
    CASE 
        WHEN password IS NOT NULL THEN 'Password Set' 
        ELSE 'No Password' 
    END as password_status
FROM `tblstudent` 
WHERE is_deleted = 0
ORDER BY student_no
LIMIT 10;