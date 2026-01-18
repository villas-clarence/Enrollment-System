<?php
// Simple test for faculty registration
$testData = [
    'role' => 'faculty',
    'existing_faculty' => true,
    'instructor_id' => 18,
    'employee_id' => 'TEST-FAC-001',
    'first_name' => 'Test',
    'last_name' => 'Faculty',
    'email' => 'test.faculty.updated@pup.edu.ph',
    'password' => 'testpassword123'
];

$jsonData = json_encode($testData);

echo "Testing Faculty Registration...\n";
echo "Sending data: " . $jsonData . "\n\n";

// Use curl to test the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/register.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

$responseData = json_decode($response, true);
if ($responseData && $responseData['success']) {
    echo "\n✅ Faculty registration test PASSED!\n";
} else {
    echo "\n❌ Faculty registration test FAILED!\n";
    if ($responseData) {
        echo "Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
}
?>
</content>
</invoke>