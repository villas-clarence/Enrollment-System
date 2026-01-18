<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/db.php';

function readJsonBody() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function getUserRole() {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($authHeader)) return null;
    
    $token = str_replace('Bearer ', '', $authHeader);
    $decoded = base64_decode($token);
    $parts = explode(':', $decoded);
    
    if (count($parts) !== 3) return null;
    
    return [
        'role' => $parts[0],
        'user_id' => intval($parts[1]),
        'timestamp' => intval($parts[2])
    ];
}

$method = $_SERVER['REQUEST_METHOD'];
$userAuth = getUserRole();

try {
    if ($method === 'GET') {
        if (!$userAuth) {
            respond(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        if ($userAuth['role'] === 'student') {
            // Get student's enrolled classes
            $stmt = $pdo->prepare('
                SELECT e.enrollment_id, e.date_enrolled, e.status, e.letter_grade,
                       sec.section_code, sec.day_pattern, sec.start_time, sec.end_time, sec.max_capacity,
                       c.course_code, c.course_title, c.units, c.lecture_hours, c.lab_hours,
                       i.first_name as instructor_first, i.last_name as instructor_last,
                       r.room_code, r.building, r.capacity as room_capacity,
                       t.term_code, t.start_date, t.end_date,
                       d.dept_name
                FROM tblenrollment e
                JOIN tblsection sec ON e.section_id = sec.section_id
                JOIN tblcourse c ON sec.course_id = c.course_id
                JOIN tblinstructor i ON sec.instructor_id = i.instructor_id
                JOIN tblroom r ON sec.room_id = r.room_id
                JOIN tblterm t ON sec.term_id = t.term_id
                JOIN tbldepartment d ON c.dept_id = d.dept_id
                WHERE e.student_id = ?
                ORDER BY t.start_date DESC, sec.start_time ASC
            ');
            $stmt->execute([$userAuth['user_id']]);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get student info
            $studentStmt = $pdo->prepare('
                SELECT s.*, p.program_name, p.program_code 
                FROM tblstudent s 
                JOIN tblprogram p ON s.program_id = p.program_id 
                WHERE s.student_id = ?
            ');
            $studentStmt->execute([$userAuth['user_id']]);
            $studentInfo = $studentStmt->fetch(PDO::FETCH_ASSOC);
            
            respond([
                'success' => true, 
                'data' => [
                    'student_info' => $studentInfo,
                    'classes' => $classes,
                    'total_classes' => count($classes),
                    'total_units' => array_sum(array_column($classes, 'units'))
                ]
            ]);
            
        } elseif ($userAuth['role'] === 'faculty') {
            // Get faculty's teaching classes
            $stmt = $pdo->prepare('
                SELECT sec.section_id, sec.section_code, sec.day_pattern, sec.start_time, sec.end_time, sec.max_capacity,
                       c.course_code, c.course_title, c.units, c.lecture_hours, c.lab_hours,
                       r.room_code, r.building, r.capacity as room_capacity,
                       t.term_code, t.start_date, t.end_date,
                       d.dept_name,
                       COUNT(e.enrollment_id) as enrolled_students
                FROM tblsection sec
                JOIN tblcourse c ON sec.course_id = c.course_id
                JOIN tblroom r ON sec.room_id = r.room_id
                JOIN tblterm t ON sec.term_id = t.term_id
                JOIN tbldepartment d ON c.dept_id = d.dept_id
                LEFT JOIN tblenrollment e ON sec.section_id = e.section_id
                WHERE sec.instructor_id = ?
                GROUP BY sec.section_id
                ORDER BY t.start_date DESC, sec.start_time ASC
            ');
            $stmt->execute([$userAuth['user_id']]);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get faculty info
            $facultyStmt = $pdo->prepare('
                SELECT i.*, d.dept_name, d.dept_code 
                FROM tblinstructor i 
                JOIN tbldepartment d ON i.dept_id = d.dept_id 
                WHERE i.instructor_id = ?
            ');
            $facultyStmt->execute([$userAuth['user_id']]);
            $facultyInfo = $facultyStmt->fetch(PDO::FETCH_ASSOC);
            
            respond([
                'success' => true, 
                'data' => [
                    'faculty_info' => $facultyInfo,
                    'classes' => $classes,
                    'total_classes' => count($classes),
                    'total_students' => array_sum(array_column($classes, 'enrolled_students'))
                ]
            ]);
            
        } else {
            // Admin gets overview of all classes
            $stmt = $pdo->prepare('
                SELECT sec.section_id, sec.section_code, sec.day_pattern, sec.start_time, sec.end_time, sec.max_capacity,
                       c.course_code, c.course_title, c.units,
                       i.first_name as instructor_first, i.last_name as instructor_last,
                       r.room_code, r.building,
                       t.term_code,
                       d.dept_name,
                       COUNT(e.enrollment_id) as enrolled_students
                FROM tblsection sec
                JOIN tblcourse c ON sec.course_id = c.course_id
                JOIN tblinstructor i ON sec.instructor_id = i.instructor_id
                JOIN tblroom r ON sec.room_id = r.room_id
                JOIN tblterm t ON sec.term_id = t.term_id
                JOIN tbldepartment d ON c.dept_id = d.dept_id
                LEFT JOIN tblenrollment e ON sec.section_id = e.section_id
                GROUP BY sec.section_id
                ORDER BY t.start_date DESC, sec.start_time ASC
            ');
            $stmt->execute();
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            respond([
                'success' => true, 
                'data' => [
                    'classes' => $classes,
                    'total_sections' => count($classes),
                    'total_students' => array_sum(array_column($classes, 'enrolled_students'))
                ]
            ]);
        }
    }
    
    if ($method === 'POST') {
        // Get students in a specific class (for faculty and admin)
        if (!$userAuth || $userAuth['role'] === 'student') {
            respond(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        $body = readJsonBody();
        $sectionId = isset($body['section_id']) ? intval($body['section_id']) : null;
        
        if (!$sectionId) {
            respond(['success' => false, 'message' => 'Missing section_id'], 400);
        }
        
        // Check if faculty has access to this section
        if ($userAuth['role'] === 'faculty') {
            $checkStmt = $pdo->prepare('SELECT section_id FROM tblsection WHERE section_id = ? AND instructor_id = ?');
            $checkStmt->execute([$sectionId, $userAuth['user_id']]);
            if (!$checkStmt->fetch()) {
                respond(['success' => false, 'message' => 'Access denied: Not your class'], 403);
            }
        }
        
        // Get students in the section
        $stmt = $pdo->prepare('
            SELECT e.enrollment_id, e.date_enrolled, e.status, e.letter_grade,
                   s.student_id, s.student_no, s.first_name, s.last_name, s.middle_initial, s.email,
                   s.gender, s.birthdate, s.year_level,
                   p.program_code, p.program_name
            FROM tblenrollment e
            JOIN tblstudent s ON e.student_id = s.student_id
            JOIN tblprogram p ON s.program_id = p.program_id
            WHERE e.section_id = ?
            ORDER BY s.last_name, s.first_name
        ');
        $stmt->execute([$sectionId]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get section info
        $sectionStmt = $pdo->prepare('
            SELECT sec.*, c.course_code, c.course_title, c.units,
                   i.first_name as instructor_first, i.last_name as instructor_last,
                   r.room_code, r.building, t.term_code
            FROM tblsection sec
            JOIN tblcourse c ON sec.course_id = c.course_id
            JOIN tblinstructor i ON sec.instructor_id = i.instructor_id
            JOIN tblroom r ON sec.room_id = r.room_id
            JOIN tblterm t ON sec.term_id = t.term_id
            WHERE sec.section_id = ?
        ');
        $sectionStmt->execute([$sectionId]);
        $sectionInfo = $sectionStmt->fetch(PDO::FETCH_ASSOC);
        
        respond([
            'success' => true,
            'data' => [
                'section_info' => $sectionInfo,
                'students' => $students,
                'total_students' => count($students)
            ]
        ]);
    }
    
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
    
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>