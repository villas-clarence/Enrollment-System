<?php
// Create tblcourse_enrollment table
require_once 'api/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS tblcourse_enrollment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        date_enrolled DATE NOT NULL,
        status VARCHAR(20) DEFAULT 'Enrolled',
        grade VARCHAR(10) NULL,
        is_deleted TINYINT(1) DEFAULT 0,
        deleted_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES tblstudent(student_id),
        FOREIGN KEY (course_id) REFERENCES tblcourse(course_id),
        UNIQUE KEY unique_enrollment (student_id, course_id)
    )";
    
    $pdo->exec($sql);
    echo "<h1 style='color:green'>✓ Table tblcourse_enrollment created successfully!</h1>";
    echo "<p>You can now use the enrollment page.</p>";
    echo "<a href='admin-enrollment.html'>Go to Enrollment Page</a>";
    
} catch (PDOException $e) {
    echo "<h1 style='color:red'>Error: " . $e->getMessage() . "</h1>";
}
?>
