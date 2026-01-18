<?php
// Simulate the API call
$_SERVER['REQUEST_METHOD'] = 'GET';

// Capture output
ob_start();
try {
    include 'api/student_list.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
$output = ob_get_clean();

echo "API Output:\n";
echo $output;
?>