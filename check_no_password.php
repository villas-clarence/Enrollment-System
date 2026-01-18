<?php
require_once 'api/db.php';

try {
    // Count students without passwords
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM tblstudent WHERE (password IS NULL OR password = "") AND is_deleted = 0');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Students without passwords: " . $result['count'] . "\n";
    
    // Show some examples
    $stmt = $pdo->prepare('
        SELECT s.first_name, s.last_name, s.student_no, s.password, p.program_code
        FROM tblstudent s
        LEFT JOIN tblprogram p ON s.program_id = p.program_id
        WHERE (s.password IS NULL OR s.password = "") AND s.is_deleted = 0
        LIMIT 5
    ');
    $stmt->execute();
    $students = $stmt->fetchAll();
    
    echo "\nExamples of students without passwords:\n";
    foreach ($students as $student) {
        echo "- {$student['first_name']} {$student['last_name']} ({$student['student_no']}) - {$student['program_code']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>