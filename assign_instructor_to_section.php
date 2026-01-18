<?php
require_once __DIR__ . '/api/db.php';

echo "<html><head><title>Assign Instructor to Section</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 30px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #1e3c72; }
    .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #1e3c72; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    a { color: #1e3c72; }
</style></head><body>";
echo "<div class='container'>";
echo "<h1>🎓 Assign Instructor to Section</h1>";

try {
    // Get Gecilie's instructor_id
    $stmt = $pdo->prepare("SELECT instructor_id, first_name, last_name, employee_id FROM tblinstructor WHERE first_name LIKE '%Gecilie%' OR last_name LIKE '%Almira%' LIMIT 1");
    $stmt->execute();
    $gecilie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gecilie) {
        echo "<div class='error'>❌ Gecilie Almirañez not found in database!</div>";
    } else {
        echo "<div class='info'>📋 Found Instructor: <strong>{$gecilie['first_name']} {$gecilie['last_name']}</strong> (ID: {$gecilie['instructor_id']}, Employee ID: {$gecilie['employee_id']})</div>";
        
        $instructorId = $gecilie['instructor_id'];
        
        // Get sections without instructor
        $stmt = $pdo->query("SELECT s.section_id, s.section_code, c.course_code, c.course_title 
                            FROM tblsection s 
                            LEFT JOIN tblcourse c ON s.course_id = c.course_id 
                            WHERE s.instructor_id IS NULL OR s.instructor_id = 0
                            LIMIT 3");
        $sectionsWithoutInstructor = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($sectionsWithoutInstructor) > 0) {
            echo "<h3>Sections without instructor (will assign to Gecilie):</h3>";
            echo "<table><tr><th>Section ID</th><th>Section Code</th><th>Course</th></tr>";
            foreach ($sectionsWithoutInstructor as $sec) {
                echo "<tr><td>{$sec['section_id']}</td><td>{$sec['section_code']}</td><td>{$sec['course_code']} - {$sec['course_title']}</td></tr>";
            }
            echo "</table>";
            
            // Assign Gecilie to these sections
            $sectionIds = array_column($sectionsWithoutInstructor, 'section_id');
            $placeholders = implode(',', array_fill(0, count($sectionIds), '?'));
            
            $stmt = $pdo->prepare("UPDATE tblsection SET instructor_id = ? WHERE section_id IN ($placeholders)");
            $params = array_merge([$instructorId], $sectionIds);
            $stmt->execute($params);
            
            $affected = $stmt->rowCount();
            echo "<div class='success'>✅ Successfully assigned {$affected} section(s) to {$gecilie['first_name']} {$gecilie['last_name']}!</div>";
        } else {
            echo "<div class='info'>ℹ️ No sections without instructor found. Checking existing sections...</div>";
            
            // Just assign to first 2 sections regardless
            $stmt = $pdo->prepare("UPDATE tblsection SET instructor_id = ? WHERE section_id IN (SELECT section_id FROM (SELECT section_id FROM tblsection LIMIT 2) as temp)");
            $stmt->execute([$instructorId]);
            
            $affected = $stmt->rowCount();
            if ($affected > 0) {
                echo "<div class='success'>✅ Assigned {$affected} section(s) to {$gecilie['first_name']} {$gecilie['last_name']}!</div>";
            }
        }
        
        // Show Gecilie's assigned sections now
        echo "<h3>Gecilie's Assigned Sections:</h3>";
        $stmt = $pdo->prepare("SELECT s.section_id, s.section_code, s.day_pattern, s.start_time, s.end_time, s.max_capacity,
                                      c.course_code, c.course_title, r.room_code
                               FROM tblsection s 
                               LEFT JOIN tblcourse c ON s.course_id = c.course_id 
                               LEFT JOIN tblroom r ON s.room_id = r.room_id
                               WHERE s.instructor_id = ?");
        $stmt->execute([$instructorId]);
        $assignedSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($assignedSections) > 0) {
            echo "<table><tr><th>Section</th><th>Course</th><th>Schedule</th><th>Room</th><th>Capacity</th></tr>";
            foreach ($assignedSections as $sec) {
                $schedule = $sec['day_pattern'] ? "{$sec['day_pattern']} {$sec['start_time']} - {$sec['end_time']}" : 'TBA';
                echo "<tr>
                    <td>{$sec['section_code']}</td>
                    <td>{$sec['course_code']} - {$sec['course_title']}</td>
                    <td>{$schedule}</td>
                    <td>" . ($sec['room_code'] ?? 'TBA') . "</td>
                    <td>{$sec['max_capacity']}</td>
                </tr>";
            }
            echo "</table>";
            echo "<div class='success'>🎉 Gecilie now has " . count($assignedSections) . " assigned class(es)! Login as faculty to see them in 'My Classes'.</div>";
        } else {
            echo "<div class='error'>❌ No sections assigned yet.</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
}

echo "<br><a href='faculty-login.html'>→ Go to Faculty Login</a> | ";
echo "<a href='faculty-dashboard.html'>→ Go to Faculty Dashboard</a>";
echo "</div></body></html>";
?>
