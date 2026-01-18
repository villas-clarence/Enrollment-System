<?php
require_once 'api/db.php';

echo "Testing student authentication...\n";

try {
    // Check if password column exists
    $stmt = $pdo->prepare('DESCRIBE tblstudent');
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Student table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    // Test student lookup
    $stmt = $pdo->prepare('SELECT student_id, student_no, first_name, last_name FROM tblstudent WHERE student_no = ? LIMIT 1');
    $stmt->execute(['2023-00424-TG-0']);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "\nStudent found:\n";
        print_r($student);
    } else {
        echo "\nStudent NOT found with student_no: 2023-00424-TG-0\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>