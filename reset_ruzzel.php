<?php
require_once 'api/db.php';

try {
    $stmt = $pdo->prepare('UPDATE tblstudent SET password = NULL WHERE student_no = ?');
    $stmt->execute(['2025-00101-TG-0']);
    echo "Reset Ruzzel's password - he can register again\n";
    
    // Verify
    $stmt = $pdo->prepare('SELECT first_name, last_name, password FROM tblstudent WHERE student_no = ?');
    $stmt->execute(['2025-00101-TG-0']);
    $student = $stmt->fetch();
    
    echo "Student: {$student['first_name']} {$student['last_name']}\n";
    echo "Has Password: " . (empty($student['password']) ? 'No' : 'Yes') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>