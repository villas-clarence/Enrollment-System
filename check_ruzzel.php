<?php
require_once 'api/db.php';

try {
    $stmt = $pdo->prepare('SELECT student_no, first_name, last_name, email, password FROM tblstudent WHERE student_no = ?');
    $stmt->execute(['2025-00101-TG-0']);
    $student = $stmt->fetch();
    
    if ($student) {
        echo "Student: {$student['first_name']} {$student['last_name']}\n";
        echo "Student No: {$student['student_no']}\n";
        echo "Email: {$student['email']}\n";
        echo "Has Password: " . (!empty($student['password']) ? 'Yes' : 'No') . "\n";
        
        // Check if email exists in other tables
        $email = $student['email'];
        
        $stmt2 = $pdo->prepare('SELECT COUNT(*) as count FROM tblinstructor WHERE email = ?');
        $stmt2->execute([$email]);
        $instructorCount = $stmt2->fetch()['count'];
        
        $stmt3 = $pdo->prepare('SELECT COUNT(*) as count FROM tblstudent WHERE email = ? AND student_no != ?');
        $stmt3->execute([$email, $student['student_no']]);
        $otherStudentCount = $stmt3->fetch()['count'];
        
        echo "Email in instructor table: $instructorCount\n";
        echo "Email in other student records: $otherStudentCount\n";
    } else {
        echo "Student not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>