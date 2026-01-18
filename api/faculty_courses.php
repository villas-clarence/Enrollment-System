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
        $instructorId = isset($_GET['instructor_id']) ? intval($_GET['instructor_id']) : null;
        
        if (!$instructorId) {
            respond(['success' => false, 'message' => 'Instructor ID is required'], 400);
        }
        
        // Get faculty info first
        $facultyQuery = '
            SELECT i.*, d.dept_name, d.dept_code 
            FROM tblinstructor i 
            LEFT JOIN tbldepartment d ON i.dept_id = d.dept_id 
            WHERE i.instructor_id = ? AND i.is_deleted = 0
        ';
        $stmt = $pdo->prepare($facultyQuery);
        $stmt->execute([$instructorId]);
        $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get assigned sections/classes for the faculty
        $assignedSections = [];
        $sectionsWithStudents = [];
        
        try {
            $assignedQuery = '
                SELECT 
                    s.section_id,
                    s.section_code,
                    c.course_id,
                    c.course_code,
                    c.course_title,
                    c.units,
                    c.lecture_hours,
                    c.lab_hours,
                    s.day_pattern,
                    s.start_time,
                    s.end_time,
                    r.room_code as room_name,
                    s.max_capacity,
                    t.term_code
                FROM tblsection s
                JOIN tblcourse c ON s.course_id = c.course_id
                LEFT JOIN tblroom r ON s.room_id = r.room_id
                LEFT JOIN tblterm t ON s.term_id = t.term_id
                WHERE s.instructor_id = ? 
                AND s.is_deleted = 0
                ORDER BY c.course_code, s.section_code
            ';
            
            $stmt = $pdo->prepare($assignedQuery);
            $stmt->execute([$instructorId]);
            $assignedSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get students for each section
            foreach ($assignedSections as &$section) {
                $studentsQuery = '
                    SELECT 
                        st.student_id,
                        st.student_no,
                        st.first_name,
                        st.last_name,
                        st.email,
                        e.enrollment_type,
                        e.status,
                        e.letter_grade,
                        e.date_enrolled
                    FROM tblenrollment e
                    JOIN tblstudent st ON e.student_id = st.student_id
                    WHERE e.section_id = ? 
                    AND e.is_deleted = 0 
                    AND e.status = "Active"
                    ORDER BY st.last_name, st.first_name
                ';
                
                $stmt = $pdo->prepare($studentsQuery);
                $stmt->execute([$section['section_id']]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $section['students'] = $students;
                $section['student_count'] = count($students);
            }
            unset($section); // Break the reference
            $sectionsWithStudents = $assignedSections;
        } catch (PDOException $e) {
            // If sections query fails, just return empty sections
            $sectionsWithStudents = [];
        }
        
        respond([
            'success' => true,
            'data' => [
                'faculty' => $faculty,
                'assigned_sections' => $sectionsWithStudents,
                'summary' => [
                    'total_sections' => count($sectionsWithStudents),
                    'total_students' => array_sum(array_column($sectionsWithStudents, 'student_count')),
                    'total_courses' => count(array_unique(array_column($sectionsWithStudents, 'course_id')))
                ]
            ]
        ]);
    }
    
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
    
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>