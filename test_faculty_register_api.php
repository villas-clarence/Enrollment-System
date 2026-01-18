<?php
require_once 'api/db.php';

// Test faculty registration for existing faculty
$testData = [
    'role' => 'faculty',
    'existing_faculty' => true,
    'instructor_id' => 18, // Test faculty we just created
    'employee_id' => 'TEST-FAC-001',
    'first_name' => 'Test',
    'last_name' => 'Faculty',
    'email' => 'test.faculty.updated@pup.edu.ph',
    'password' => 'testpassword123'
];

echo "Testing Faculty Registration API...\n";
echo "Data to send:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Simulate POST request to register.php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Capture the JSON input
$GLOBALS['test_input'] = json_encode($testData);

// Override file_get_contents for testing
function file_get_contents($filename) {
    if ($filename === 'php://input') {
        return $GLOBALS['test_input'];
    }
    return \file_get_contents($filename);
}

// Capture output
ob_start();

try {
    include 'api/register.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
echo "API Response:\n";
echo $output . "\n";

// Check if registration was successful
$response = json_decode($output, true);
if ($response && $response['success']) {
    echo "\n✅ Faculty registration test PASSED!\n";
    
    // Verify the password was set
    $stmt = $pdo->prepare('SELECT password FROM tblinstructor WHERE instructor_id = ?');
    $stmt->execute([18]);
    $result = $stmt->fetch();
    
    if ($result && !empty($result['password'])) {
        echo "✅ Password was successfully set in database\n";
    } else {
        echo "❌ Password was not set in database\n";
    }
} else {
    echo "\n❌ Faculty registration test FAILED!\n";
    if ($response) {
        echo "Error: " . ($response['message'] ?? 'Unknown error') . "\n";
    }
}
?>
</content>
</invoke>