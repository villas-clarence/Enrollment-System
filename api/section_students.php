<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/db.php';

try {
    $section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
    
    if ($section_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid section ID']);
        exit;
    }
    
    // Get section details
    $sectionStmt = $pdo->prepare("
        SELECT 
            s.section_id,
            s.section_code,
            c.course_code,
            c.course_title,
            s.max_capacity
        FROM tblsection s
        LEFT JOIN tblcourse c ON s.course_id = c.course_id
        WHERE s.section_id = ?
    ");
    $sectionStmt->execute([$section_id]);
    $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$section) {
        echo json_encode(['success' => false, 'message' => 'Section not found']);
        exit;
    }
    
    // Get enrolled students
    $studentsStmt = $pdo->prepare("
        SELECT 
            st.student_id,
            st.student_no as student_number,
            st.first_name,
            st.last_name,
            st.email,
            e.enrollment_id,
            e.date_enrolled
        FROM tblenrollment e
        INNER JOIN tblstudent st ON e.student_id = st.student_id
        WHERE e.section_id = ?
        ORDER BY st.last_name, st.first_name
    ");
    $studentsStmt->execute([$section_id]);
    $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'section' => $section,
        'students' => $students,
        'total_students' => count($students)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
