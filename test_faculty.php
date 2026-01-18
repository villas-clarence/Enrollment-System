<?php
require_once 'api/db.php';

echo "Checking faculty/instructor setup...\n\n";

try {
    // Check instructor table structure
    $stmt = $pdo->prepare('DESCRIBE tblinstructor');
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Instructor table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    // Get all instructors
    $stmt = $pdo->prepare('SELECT * FROM tblinstructor WHERE is_deleted = 0 ORDER BY instructor_id');
    $stmt->execute();
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nTotal instructors found: " . count($instructors) . "\n\n";
    
    foreach ($instructors as $instructor) {
        $id = $instructor['instructor_id'];
        $firstName = $instructor['first_name'];
        $lastName = $instructor['last_name'];
        $email = $instructor['email'];
        $employeeId = isset($instructor['employee_id']) ? $instructor['employee_id'] : 'NOT SET';
        $password = isset($instructor['password']) ? ($instructor['password'] ? 'SET' : 'NOT SET') : 'COLUMN NOT EXISTS';
        
        echo "ID: {$id} | {$firstName} {$lastName}\n";
        echo "  Email: {$email}\n";
        echo "  Employee ID: {$employeeId}\n";
        echo "  Password: {$password}\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>