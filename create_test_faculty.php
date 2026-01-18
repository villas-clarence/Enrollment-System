<?php
require_once 'api/db.php';

try {
    // Create a test faculty member without password for testing registration
    $stmt = $pdo->prepare('
        INSERT INTO tblinstructor (
            employee_id, 
            first_name, 
            last_name, 
            email, 
            dept_id, 
            position, 
            specialization, 
            password, 
            is_deleted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
    ');
    
    $result = $stmt->execute([
        'TEST-FAC-001',
        'Test',
        'Faculty',
        'test.faculty@pup.edu.ph',
        1, // CCIS department
        'Instructor',
        'Testing',
        null // No password - needs registration
    ]);
    
    if ($result) {
        echo "✅ Test faculty created successfully!\n";
        echo "Employee ID: TEST-FAC-001\n";
        echo "Name: Test Faculty\n";
        echo "Email: test.faculty@pup.edu.ph\n";
        echo "Department: CCIS\n";
        echo "Status: Needs Registration (no password)\n";
    } else {
        echo "❌ Failed to create test faculty\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
</content>
</invoke>