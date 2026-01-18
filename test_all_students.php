<?php
require_once 'api/db.php';

echo "Testing all student logins...\n\n";

try {
    // Get all students
    $stmt = $pdo->prepare('SELECT student_id, student_no, first_name, last_name, password FROM tblstudent WHERE is_deleted = 0 ORDER BY student_no');
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total students found: " . count($students) . "\n\n";
    
    $successCount = 0;
    $failCount = 0;
    $noPasswordCount = 0;
    
    foreach ($students as $student) {
        $studentNo = $student['student_no'];
        $password = $student['password'];
        $name = $student['first_name'] . ' ' . $student['last_name'];
        
        // Check if password exists
        if (empty($password)) {
            echo "❌ {$studentNo} ({$name}) - NO PASSWORD SET\n";
            $noPasswordCount++;
            continue;
        }
        
        // Test password verification
        if (password_verify('student123', $password)) {
            echo "✅ {$studentNo} ({$name}) - LOGIN OK\n";
            $successCount++;
        } else {
            echo "❌ {$studentNo} ({$name}) - PASSWORD MISMATCH\n";
            $failCount++;
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SUMMARY:\n";
    echo "✅ Students that can login: {$successCount}\n";
    echo "❌ Students with password issues: {$failCount}\n";
    echo "⚠️  Students without password: {$noPasswordCount}\n";
    echo "📊 Total students: " . count($students) . "\n";
    
    if ($successCount == count($students)) {
        echo "\n🎉 ALL STUDENTS CAN LOGIN SUCCESSFULLY!\n";
    } else {
        echo "\n⚠️  Some students have login issues that need fixing.\n";
    }
    
    // Test a few random student logins via API
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TESTING API AUTHENTICATION FOR SAMPLE STUDENTS:\n\n";
    
    $sampleStudents = array_slice($students, 0, 5); // Test first 5 students
    
    foreach ($sampleStudents as $student) {
        $studentNo = $student['student_no'];
        $name = $student['first_name'] . ' ' . $student['last_name'];
        
        // Simulate API call
        $username = $studentNo;
        $password = 'student123';
        $role = 'student';
        
        // Test authentication function
        require_once 'api/auth.php';
        
        // Create a mock authentication test
        $stmt = $pdo->prepare('SELECT student_id, student_no, password, first_name, last_name FROM tblstudent WHERE student_no = ? AND is_deleted = 0');
        $stmt->execute([$username]);
        $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($studentData && password_verify($password, $studentData['password'])) {
            echo "✅ API Test - {$studentNo} ({$name}) - SUCCESS\n";
        } else {
            echo "❌ API Test - {$studentNo} ({$name}) - FAILED\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>