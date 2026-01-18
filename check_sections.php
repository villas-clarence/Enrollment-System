<?php
require_once __DIR__ . '/api/db.php';

echo "<html><head><title>Check Sections</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 30px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
    h1, h2 { color: #1e3c72; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 12px; }
    th { background: #1e3c72; color: white; }
    .info { background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .success { background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style></head><body>";
echo "<div class='container'>";
echo "<h1>🔍 Check Sections & Instructors</h1>";

try {
    // Check tblsection structure
    echo "<h2>1. tblsection Table Structure:</h2>";
    $stmt = $pdo->query("DESCRIBE tblsection");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";
    
    // Check all sections
    echo "<h2>2. All Sections in Database:</h2>";
    $stmt = $pdo->query("SELECT s.*, c.course_code, c.course_title, i.first_name, i.last_name 
                         FROM tblsection s 
                         LEFT JOIN tblcourse c ON s.course_id = c.course_id 
                         LEFT JOIN tblinstructor i ON s.instructor_id = i.instructor_id
                         LIMIT 20");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>Total sections found: " . count($sections) . "</div>";
    
    if (count($sections) > 0) {
        echo "<table><tr><th>ID</th><th>Code</th><th>Course</th><th>Instructor ID</th><th>Instructor Name</th><th>is_deleted</th></tr>";
        foreach ($sections as $sec) {
            $instName = $sec['first_name'] ? "{$sec['first_name']} {$sec['last_name']}" : '<span style="color:red">NULL</span>';
            $isDeleted = isset($sec['is_deleted']) ? $sec['is_deleted'] : 'N/A';
            echo "<tr>
                <td>{$sec['section_id']}</td>
                <td>{$sec['section_code']}</td>
                <td>{$sec['course_code']}</td>
                <td>{$sec['instructor_id']}</td>
                <td>{$instName}</td>
                <td>{$isDeleted}</td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='color:red'>❌ No sections found in database!</div>";
    }
    
    // Check Gecilie
    echo "<h2>3. Gecilie's Info:</h2>";
    $stmt = $pdo->query("SELECT * FROM tblinstructor WHERE instructor_id = 6");
    $gecilie = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($gecilie) {
        echo "<div class='success'>✅ Found: {$gecilie['first_name']} {$gecilie['last_name']} (ID: {$gecilie['instructor_id']})</div>";
    } else {
        echo "<div style='color:red'>❌ Instructor ID 6 not found!</div>";
    }
    
    // Force assign Gecilie to section 24, 25, 26
    echo "<h2>4. Force Assign Gecilie to Sections:</h2>";
    $stmt = $pdo->prepare("UPDATE tblsection SET instructor_id = 6 WHERE section_id IN (24, 25, 26)");
    $stmt->execute();
    echo "<div class='success'>✅ Assigned instructor_id=6 to sections 24, 25, 26. Rows affected: " . $stmt->rowCount() . "</div>";
    
    // Verify
    echo "<h2>5. Verify Gecilie's Sections:</h2>";
    $stmt = $pdo->query("SELECT s.section_id, s.section_code, s.instructor_id, c.course_code 
                         FROM tblsection s 
                         LEFT JOIN tblcourse c ON s.course_id = c.course_id 
                         WHERE s.instructor_id = 6");
    $geciSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($geciSections) > 0) {
        echo "<table><tr><th>Section ID</th><th>Section Code</th><th>Course</th><th>Instructor ID</th></tr>";
        foreach ($geciSections as $sec) {
            echo "<tr><td>{$sec['section_id']}</td><td>{$sec['section_code']}</td><td>{$sec['course_code']}</td><td>{$sec['instructor_id']}</td></tr>";
        }
        echo "</table>";
        echo "<div class='success'>🎉 Gecilie now has " . count($geciSections) . " sections! Go to Faculty Dashboard to check.</div>";
    } else {
        echo "<div style='color:red'>❌ Still no sections for Gecilie!</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color:red'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "<br><a href='faculty-dashboard.html'>→ Go to Faculty Dashboard</a>";
echo "</div></body></html>";
?>
