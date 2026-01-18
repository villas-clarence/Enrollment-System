<?php
// Test script to check sections in database
header('Content-Type: text/html; charset=utf-8');
require_once 'api/db.php';

echo "<h1>Database Section Check</h1>";

// Count sections
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tblsection");
$count = $stmt->fetch();
echo "<h2>Total Sections in tblsection: <strong style='color:blue'>{$count['total']}</strong></h2>";

// Count courses
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tblcourse");
$count = $stmt->fetch();
echo "<h2>Total Courses in tblcourse: <strong style='color:green'>{$count['total']}</strong></h2>";

// List all sections
echo "<h2>All Sections:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse'>";
echo "<tr style='background:#1e3c72;color:white'>
        <th>ID</th>
        <th>Section Code</th>
        <th>Course</th>
        <th>Day</th>
        <th>Time</th>
        <th>Room</th>
        <th>Capacity</th>
      </tr>";

$stmt = $pdo->query("SELECT s.*, c.course_code, c.course_title 
                     FROM tblsection s 
                     LEFT JOIN tblcourse c ON s.course_id = c.course_id 
                     ORDER BY s.section_id");
$sections = $stmt->fetchAll();

foreach ($sections as $s) {
    echo "<tr>
            <td>{$s['section_id']}</td>
            <td>{$s['section_code']}</td>
            <td>{$s['course_code']} - {$s['course_title']}</td>
            <td>{$s['day_pattern']}</td>
            <td>{$s['start_time']} - {$s['end_time']}</td>
            <td>{$s['room_id']}</td>
            <td>{$s['max_capacity']}</td>
          </tr>";
}
echo "</table>";

// List all courses
echo "<h2>All Courses:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse'>";
echo "<tr style='background:#28a745;color:white'>
        <th>ID</th>
        <th>Course Code</th>
        <th>Course Title</th>
        <th>Units</th>
      </tr>";

$stmt = $pdo->query("SELECT * FROM tblcourse ORDER BY course_id");
$courses = $stmt->fetchAll();

foreach ($courses as $c) {
    echo "<tr>
            <td>{$c['course_id']}</td>
            <td>{$c['course_code']}</td>
            <td>{$c['course_title']}</td>
            <td>{$c['units']}</td>
          </tr>";
}
echo "</table>";
?>
