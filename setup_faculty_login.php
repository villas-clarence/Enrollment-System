<?php
require_once 'api/db.php';

echo "Setting up faculty login system...\n\n";

try {
    // Add employee_id and password columns to tblinstructor
    echo "1. Adding employee_id column...\n";
    try {
        $pdo->exec('ALTER TABLE tblinstructor ADD COLUMN employee_id VARCHAR(50) NULL AFTER instructor_id');
        echo "✅ employee_id column added successfully!\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✅ employee_id column already exists\n";
        } else {
            throw $e;
        }
    }
    
    echo "2. Adding password column...\n";
    try {
        $pdo->exec('ALTER TABLE tblinstructor ADD COLUMN password VARCHAR(255) NULL AFTER email');
        echo "✅ password column added successfully!\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✅ password column already exists\n";
        } else {
            throw $e;
        }
    }
    
    echo "3. Adding position and specialization columns...\n";
    try {
        $pdo->exec('ALTER TABLE tblinstructor ADD COLUMN position VARCHAR(100) DEFAULT "Instructor" AFTER password');
        echo "✅ position column added successfully!\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✅ position column already exists\n";
        }
    }
    
    try {
        $pdo->exec('ALTER TABLE tblinstructor ADD COLUMN specialization VARCHAR(255) NULL AFTER position');
        echo "✅ specialization column added successfully!\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✅ specialization column already exists\n";
        }
    }
    
    echo "\n4. Setting up employee IDs and passwords for existing faculty...\n";
    
    // Get all instructors
    $stmt = $pdo->prepare('SELECT instructor_id, first_name, last_name, email FROM tblinstructor WHERE is_deleted = 0');
    $stmt->execute();
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hashedPassword = password_hash('faculty123', PASSWORD_DEFAULT);
    
    foreach ($instructors as $instructor) {
        $id = $instructor['instructor_id'];
        $firstName = $instructor['first_name'];
        $lastName = $instructor['last_name'];
        $email = $instructor['email'];
        
        // Generate employee ID (FAC-YYYY-XXX format)
        $employeeId = 'FAC-2024-' . str_pad($id, 3, '0', STR_PAD_LEFT);
        
        // Update instructor with employee_id and password
        $stmt = $pdo->prepare('UPDATE tblinstructor SET employee_id = ?, password = ? WHERE instructor_id = ?');
        $stmt->execute([$employeeId, $hashedPassword, $id]);
        
        echo "✅ {$employeeId} - {$firstName} {$lastName} ({$email})\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎉 FACULTY LOGIN SETUP COMPLETE!\n\n";
    
    echo "Faculty can now login with:\n";
    echo "👤 Username: Employee ID (e.g., FAC-2024-001) OR Email\n";
    echo "🔒 Password: faculty123\n";
    echo "🎭 Role: Faculty\n\n";
    
    echo "Sample Faculty Logins:\n";
    $stmt = $pdo->prepare('SELECT employee_id, first_name, last_name, email FROM tblinstructor WHERE is_deleted = 0 LIMIT 5');
    $stmt->execute();
    $sampleFaculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sampleFaculty as $faculty) {
        echo "• Username: {$faculty['employee_id']} or {$faculty['email']}\n";
        echo "  Name: {$faculty['first_name']} {$faculty['last_name']}\n";
        echo "  Password: faculty123\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>