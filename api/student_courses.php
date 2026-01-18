<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/db.php';

function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
        
        if (!$studentId) {
            respond(['success' => false, 'message' => 'Student ID is required'], 400);
        }
        
        // Get enrolled courses from section enrollment (tblenrollment)
        $enrolledQuery = '
            SELECT 
                c.course_id,
                c.course_code,
                c.course_title,
                c.units,
                c.lecture_hours,
                c.lab_hours,
                s.section_code,
                s.day_pattern,
                s.start_time,
                s.end_time,
                r.room_code as room_name,
                CONCAT(i.first_name, " ", i.last_name) as instructor_name,
                e.status,
                e.letter_grade,
                e.date_enrolled,
                e.enrollment_id
            FROM tblenrollment e
            JOIN tblsection s ON e.section_id = s.section_id
            JOIN tblcourse c ON s.course_id = c.course_id
            LEFT JOIN tblroom r ON s.room_id = r.room_id
            LEFT JOIN tblinstructor i ON s.instructor_id = i.instructor_id
            WHERE e.student_id = ? 
            AND e.is_deleted = 0 
            AND e.status IN ("Active", "Completed")
            ORDER BY c.course_code
        ';
        
        $stmt = $pdo->prepare($enrolledQuery);
        $stmt->execute([$studentId]);
        $enrolledCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get available courses (sections that are available for enrollment)
        // Filter by student's year level and exclude courses already enrolled in
        $enrolledCourseIds = array_column($enrolledCourses, 'course_id');
        $excludeClause = '';
        $params = [$studentId]; // First parameter for student year level
        
        if (!empty($enrolledCourseIds)) {
            $placeholders = str_repeat('?,', count($enrolledCourseIds) - 1) . '?';
            $excludeClause = "AND c.course_id NOT IN ($placeholders)";
            $params = array_merge($params, $enrolledCourseIds);
        }
        
        $availableQuery = "
            SELECT 
                c.course_id,
                c.course_code,
                c.course_title,
                c.units,
                c.year_level,
                c.lecture_hours,
                c.lab_hours,
                COALESCE(section_data.available_sections, 0) as available_sections,
                COALESCE(section_data.sections, 'No sections available') as sections
            FROM tblcourse c
            JOIN tblstudent st ON st.student_id = ?
            LEFT JOIN (
                SELECT 
                    s.course_id,
                    COUNT(s.section_id) as available_sections,
                    GROUP_CONCAT(DISTINCT s.section_code ORDER BY s.section_code) as sections
                FROM tblsection s
                WHERE s.is_deleted = 0
                GROUP BY s.course_id
            ) section_data ON c.course_id = section_data.course_id
            WHERE c.is_deleted = 0 
            AND c.year_level <= st.year_level
            $excludeClause
            ORDER BY c.year_level, c.course_code
        ";
        
        $stmt = $pdo->prepare($availableQuery);
        $stmt->execute($params);
        $availableCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get student info for context
        $studentQuery = '
            SELECT s.*, p.program_name, p.program_code 
            FROM tblstudent s 
            LEFT JOIN tblprogram p ON s.program_id = p.program_id 
            WHERE s.student_id = ?
        ';
        $stmt = $pdo->prepare($studentQuery);
        $stmt->execute([$studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        respond([
            'success' => true,
            'data' => [
                'student' => $student,
                'enrolled_courses' => $enrolledCourses,
                'available_courses' => $availableCourses,
                'summary' => [
                    'enrolled_count' => count($enrolledCourses),
                    'available_count' => count($availableCourses),
                    'total_enrolled_units' => array_sum(array_column($enrolledCourses, 'units'))
                ]
            ]
        ]);
    }
    
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
    
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>