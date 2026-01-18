<?php
// Simulate the registration API call
$_SERVER['REQUEST_METHOD'] = 'POST';

// Test data for Ruzzel
$testData = [
    'role' => 'student',
    'existing_student' => true,
    'student_no' => '2025-00101-TG-0',
    'first_name' => 'Ruzzel Andrei Velasquez',
    'last_name' => 'Abanang',
    'email' => 'ruzzel.test@pup.edu.ph',
    'password' => 'student123',
    'contact_number' => '09123456789'
];

// Set the input data
file_put_contents('php://input', json_encode($testData));

// Capture output
ob_start();
try {
    // Mock the input
    $_POST = $testData;
    
    // Include the API file
    include 'api/register.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
$output = ob_get_clean();

echo "API Test Output:\n";
echo $output;
?>