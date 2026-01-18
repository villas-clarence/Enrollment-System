<?php
// Test the "already has account" logic
require_once 'api/db.php';

echo "Testing Faculty Account Status Logic...\n\n";

// Test 1: Faculty with password (should redirect to login)
$stmt = $pdo->prepare('SELECT instructor_id, first_name, last_name, password FROM tblinstructor WHERE employee_id = ?');
$stmt->execute(['TEST-FAC-001']);
$faculty = $stmt->fetch();

if ($faculty) {
    echo "Test Faculty: {$faculty['first_name']} {$faculty['last_name']}\n";
    echo "Has Password: " . (!empty($faculty['password']) ? "YES" : "NO") . "\n";
    echo "Expected Behavior: " . (!empty($faculty['password']) ? "Redirect to Login" : "Show Registration Form") . "\n\n";
}

// Test 2: Create another faculty without password
try {
    $stmt = $pdo->prepare('
        INSERT INTO tblinstructor (
            employee_id, first_name, last_name, email, dept_id, position, password, is_deleted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    ');
    
    $result = $stmt->execute([
        'TEST-FAC-002',
        'New',
        'Faculty',
        'new.faculty@pup.edu.ph',
        2, // Education department
        'Instructor',
        null // No password
    ]);
    
    if ($result) {
        echo "✅ Created second test faculty (TEST-FAC-002) without password\n";
        echo "Expected Behavior: Show Registration Form\n\n";
    }
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "ℹ️ Test faculty TEST-FAC-002 already exists\n\n";
    } else {
        echo "❌ Error creating test faculty: " . $e->getMessage() . "\n\n";
    }
}

// Show current faculty status summary
echo "=== Faculty Registration Status Summary ===\n";
$stmt = $pdo->query('
    SELECT 
        i.employee_id,
        i.first_name,
        i.last_name,
        d.dept_code,
        CASE 
            WHEN i.password IS NULL OR i.password = "" THEN "Needs Registration"
            ELSE "Has Account"
        END as status
    FROM tblinstructor i
    LEFT JOIN tbldepartment d ON i.dept_id = d.dept_id
    WHERE i.is_deleted = 0
    ORDER BY i.employee_id
');

$faculty_list = $stmt->fetchAll();
foreach ($faculty_list as $f) {
    $status_icon = $f['status'] === 'Has Account' ? '🔐' : '📝';
    echo "{$status_icon} {$f['employee_id']} - {$f['first_name']} {$f['last_name']} ({$f['dept_code']}) - {$f['status']}\n";
}

echo "\n✅ Faculty registration system is ready for testing!\n";
echo "📋 You can now test the faculty registration page at: http://localhost:8000/faculty_register.html\n";
?>
</content>
</invoke>