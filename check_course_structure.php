<?php
require_once 'api/db.php';

echo "<h1>Course Table Structure</h1>";

// Check columns in tblcourse
$stmt = $pdo->query("DESCRIBE tblcourse");
$columns = $stmt->fetchAll();

echo "<h2>Columns in tblcourse:</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse'>";
echo "<tr style='background:#1e3c72;color:white'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
}
echo "</table>";

// Check if year_level exists
$hasYearLevel = false;
foreach ($columns as $col) {
    if ($col['Field'] == 'year_level') $hasYearLevel = true;
}

echo "<br><h2>Status:</h2>";
if ($hasYearLevel) {
    echo "<p style='color:green; font-size:1.2rem;'>✓ year_level column EXISTS in tblcourse</p>";
    
    // Show courses by year level
    echo "<h3>Courses by Year Level:</h3>";
    $stmt = $pdo->query("SELECT year_level, COUNT(*) as count FROM tblcourse GROUP BY year_level ORDER BY year_level");
    $groups = $stmt->fetchAll();
    foreach ($groups as $g) {
        echo "<p>Year {$g['year_level']}: {$g['count']} courses</p>";
    }
} else {
    echo "<p style='color:red; font-size:1.2rem;'>✗ year_level column DOES NOT EXIST in tblcourse</p>";
    echo "<p>Need to add this column to filter courses by year level.</p>";
}
?>
