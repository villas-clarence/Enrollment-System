<?php
header('Content-Type: text/plain');

try {
    require_once 'api/db.php';
    
    echo "✅ Database connection successful!\n\n";
    
    // Check if student table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'tblstudent'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Student table exists\n";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE tblstudent");
        echo "\n📋 Table structure:\n";
        echo str_pad('Field', 20) . str_pad('Type', 20) . str_pad('Null', 10) . str_pad('Key', 10) . "\n";
        echo str_repeat('-', 60) . "\n";
        while ($row = $stmt->fetch()) {
            echo str_pad($row['Field'], 20) . 
                 str_pad($row['Type'], 20) . 
                 str_pad($row['Null'], 10) . 
                 str_pad($row['Key'], 10) . "\n";
        }
        
        // Count records
        $count = $pdo->query("SELECT COUNT(*) as count FROM tblstudent")->fetch()['count'];
        echo "\n📊 Total students: " . $count . "\n";
        
        // Show first few records
        if ($count > 0) {
            echo "\n📝 Sample records:\n";
            $students = $pdo->query("SELECT student_id, student_no, first_name, last_name FROM tblstudent LIMIT 3");
            while ($student = $students->fetch()) {
                echo "- " . $student['student_no'] . ": " . $student['first_name'] . " " . $student['last_name'] . "\n";
            }
        }
    } else {
        echo "❌ Student table does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "\nPlease check your database credentials in api/db.php\n";
    }
}
?>
