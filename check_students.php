<?php
require_once 'api/db.php';

echo "<h1>Student Count Check</h1>";

// Total students in database
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tblstudent");
$total = $stmt->fetch()['total'];
echo "<p>Total students in tblstudent: <strong>{$total}</strong></p>";

// Not deleted students
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tblstudent WHERE is_deleted = 0 OR is_deleted IS NULL");
$active = $stmt->fetch()['total'];
echo "<p>Active students (not deleted): <strong>{$active}</strong></p>";

// Check API response
echo "<h2>API Response:</h2>";
$stmt = $pdo->query("SELECT student_id, student_no, first_name, last_name, is_deleted FROM tblstudent ORDER BY student_id LIMIT 100");
$students = $stmt->fetchAll();

echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
echo "<tr style='background:#1e3c72;color:white'><th>ID</th><th>Student No</th><th>Name</th><th>is_deleted</th></tr>";
foreach ($students as $s) {
    $deleted = $s['is_deleted'] ? 'YES' : 'NO';
    $bg = $s['is_deleted'] ? '#f8d7da' : '#d4edda';
    echo "<tr style='background:{$bg}'><td>{$s['student_id']}</td><td>{$s['student_no']}</td><td>{$s['first_name']} {$s['last_name']}</td><td>{$deleted}</td></tr>";
}
echo "</table>";
?>
