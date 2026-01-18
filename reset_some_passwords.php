<?php
require_once 'api/db.php';

try {
    // Reset passwords for some students to make them available for registration
    // Let's reset passwords for students from different years
    
    $studentsToReset = [
        '2023-00424-TG-0', // Aleck Alejandro
        '2023-00425-TG-0', // Gener Andaya
        '2024-00309-TG-0', // Roland Acido
        '2024-00310-TG-0', // Yuan Paolo Allego
        '2025-00101-TG-0', // Ruzzel Abanang
        '2025-00102-TG-0', // Daniel Adto
    ];
    
    foreach ($studentsToReset as $studentNo) {
        $stmt = $pdo->prepare('UPDATE tblstudent SET password = NULL WHERE student_no = ?');
        $stmt->execute([$studentNo]);
        echo "Reset password for student: $studentNo\n";
    }
    
    echo "\nDone! " . count($studentsToReset) . " students are now available for registration.\n";
    
    // Verify the changes
    $stmt = $pdo->prepare('
        SELECT s.first_name, s.last_name, s.student_no, p.program_code, s.year_level
        FROM tblstudent s
        LEFT JOIN tblprogram p ON s.program_id = p.program_id
        WHERE s.password IS NULL AND s.is_deleted = 0
        ORDER BY s.year_level, s.student_no
    ');
    $stmt->execute();
    $availableStudents = $stmt->fetchAll();
    
    echo "\nStudents now available for registration:\n";
    foreach ($availableStudents as $student) {
        echo "- {$student['first_name']} {$student['last_name']} ({$student['student_no']}) - {$student['program_code']} Year {$student['year_level']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>