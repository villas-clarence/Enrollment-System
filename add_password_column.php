<?php
require_once 'api/db.php';

try {
    echo "Adding password column to tblstudent...\n";
    
    // Add password column
    $pdo->exec('ALTER TABLE tblstudent ADD COLUMN password VARCHAR(255) NULL AFTER program_id');
    echo "✅ Password column added successfully!\n";
    
    // Set default password for all existing students
    $hashedPassword = password_hash('student123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE tblstudent SET password = ? WHERE password IS NULL');
    $stmt->execute([$hashedPassword]);
    
    $affected = $stmt->rowCount();
    echo "✅ Default password set for $affected students!\n";
    
    echo "\nNow all students can login with:\n";
    echo "- Username: Their student_no (e.g., 2023-00424-TG-0)\n";
    echo "- Password: student123\n";
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Password column already exists. Setting default passwords...\n";
        
        // Just set default passwords
        $hashedPassword = password_hash('student123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE tblstudent SET password = ? WHERE password IS NULL OR password = ""');
        $stmt->execute([$hashedPassword]);
        
        $affected = $stmt->rowCount();
        echo "✅ Default password set for $affected students!\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
?>