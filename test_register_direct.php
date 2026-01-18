<?php
require_once 'api/db.php';

// Test the registration logic directly
try {
    $data = [
        'role' => 'student',
        'existing_student' => true,
        'student_no' => '2025-00101-TG-0',
        'first_name' => 'Ruzzel Andrei Velasquez',
        'last_name' => 'Abanang',
        'email' => 'ruzzel.test@pup.edu.ph',
        'password' => 'student123'
    ];
    
    echo "Testing registration for: {$data['first_name']} {$data['last_name']}\n";
    
    // Check if student exists and doesn't have account yet
    $stmt = $pdo->prepare('SELECT student_id, first_name, last_name, password FROM tblstudent WHERE student_no = ? AND is_deleted = 0');
    $stmt->execute([$data['student_no']]);
    $existingStudent = $stmt->fetch();
    
    if (!$existingStudent) {
        echo "ERROR: Student not found in database\n";
        exit;
    }
    
    echo "Student found: {$existingStudent['first_name']} {$existingStudent['last_name']}\n";
    echo "Current password: " . (empty($existingStudent['password']) ? 'None' : 'Has password') . "\n";
    
    if (!empty($existingStudent['password'])) {
        echo "ERROR: Student already has an account\n";
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Update student with password and email
    $stmt = $pdo->prepare('UPDATE tblstudent SET password = ?, email = ? WHERE student_no = ?');
    $result = $stmt->execute([$hashedPassword, $data['email'], $data['student_no']]);
    
    if ($result) {
        echo "SUCCESS: Account created successfully!\n";
        
        // Verify the update
        $stmt = $pdo->prepare('SELECT email, password FROM tblstudent WHERE student_no = ?');
        $stmt->execute([$data['student_no']]);
        $updated = $stmt->fetch();
        
        echo "Updated email: {$updated['email']}\n";
        echo "Has password: " . (!empty($updated['password']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "ERROR: Failed to update student record\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>