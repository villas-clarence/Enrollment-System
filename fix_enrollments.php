<?php
require_once 'api/db.php';

echo "<h1>Fix Course Enrollments</h1>";

// Show current enrollments
echo "<h2>Current Enrollments:</h2>";
$stmt = $pdo->query("SELECT ce.*, c.course_code, c.course_title, s.student_no, s.first_name, s.last_name 
                     FROM tblcourse_enrollment ce 
                     LEFT JOIN tblcourse c ON ce.course_id = c.course_id
                     LEFT JOIN tblstudent s ON ce.student_id = s.student_id
                     ORDER BY ce.student_id, ce.id");
$enrollments = $stmt->fetchAll();

echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
echo "<tr style='background:#1e3c72;color:white'><th>ID</th><th>Student</th><th>Course</th><th>Status</th><th>Action</th></tr>";
foreach ($enrollments as $e) {
    echo "<tr>
            <td>{$e['id']}</td>
            <td>{$e['first_name']} {$e['last_name']} ({$e['student_no']})</td>
            <td>{$e['course_code']} - {$e['course_title']}</td>
            <td>{$e['status']}</td>
            <td><a href='?delete={$e['id']}' style='color:red;'>Delete</a></td>
          </tr>";
}
echo "</table>";

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM tblcourse_enrollment WHERE id = ?")->execute([$id]);
    echo "<script>window.location.href='fix_enrollments.php';</script>";
}

// Clear all button
if (isset($_GET['clear_all'])) {
    $pdo->exec("DELETE FROM tblcourse_enrollment");
    echo "<script>window.location.href='fix_enrollments.php';</script>";
}

echo "<br><a href='?clear_all=1' style='background:red;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;' onclick=\"return confirm('Delete ALL enrollments?')\">Clear All Enrollments</a>";
echo " <a href='admin-enrollment.html' style='background:green;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to Enrollment Page</a>";
?>
