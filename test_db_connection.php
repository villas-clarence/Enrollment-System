<?php
require_once 'api/db.php';

try {
    $stmt = $pdo->prepare('
        SELECT 
            s.student_id,
            s.student_no,
            s.first_name,
            s.last_name,
            s.middle_name,
            s.email,
            s.year_level,
            s.program_id,
            s.password,
            p.program_code,
            p.program_name
        FROM tblstudent s
        LEFT JOIN tblprogram p ON s.program_id = p.program_id
        WHERE s.is_deleted = 0
        ORDER BY s.year_level, s.student_no LIMIT 5
    ');
    
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "API Test Results:\n";
    echo "Found " . count($students) . " students\n\n";
    
    foreach ($students as $student) {
        $hasPassword = !empty($student['password']) ? 'Yes' : 'No';
        $middleName = $student['middle_name'] ? " {$student['middle_name']}" : '';
        echo "- {$student['first_name']}{$middleName} {$student['last_name']} ({$student['student_no']}) - Program: {$student['program_code']} - Has password: {$hasPassword}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>