<?php
// api/course_enrollment.php - Simple course enrollment (without sections)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { echo json_encode(['success'=>true]); exit; }

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get all course enrollments for a student
        $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
        
        $sql = "SELECT ce.*, c.course_code, c.course_title, c.units, c.year_level, c.lecture_hours, c.lab_hours,
                       s.student_no, s.first_name, s.last_name
                FROM tblcourse_enrollment ce
                JOIN tblcourse c ON ce.course_id = c.course_id
                JOIN tblstudent s ON ce.student_id = s.student_id
                WHERE ce.is_deleted = 0";
        
        if ($student_id > 0) {
            $sql .= " AND ce.student_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id]);
        } else {
            $stmt = $pdo->query($sql);
        }
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success'=>true, 'data'=>$data]);
    }
    
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $student_id = isset($input['student_id']) ? intval($input['student_id']) : 0;
        $course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;
        $date_enrolled = isset($input['date_enrolled']) ? $input['date_enrolled'] : date('Y-m-d');
        $status = isset($input['status']) ? $input['status'] : 'Enrolled';
        
        if ($student_id <= 0 || $course_id <= 0) {
            echo json_encode(['success'=>false, 'message'=>'Invalid student or course ID']);
            exit;
        }
        
        // Check if already enrolled
        $check = $pdo->prepare("SELECT id FROM tblcourse_enrollment WHERE student_id = ? AND course_id = ? AND is_deleted = 0");
        $check->execute([$student_id, $course_id]);
        if ($check->fetch()) {
            echo json_encode(['success'=>false, 'message'=>'Student already enrolled in this course']);
            exit;
        }
        
        $sql = "INSERT INTO tblcourse_enrollment (student_id, course_id, date_enrolled, status) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $course_id, $date_enrolled, $status]);
        
        echo json_encode(['success'=>true, 'message'=>'Enrolled successfully', 'id'=>$pdo->lastInsertId()]);
    }
    
    elseif ($method === 'DELETE') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            echo json_encode(['success'=>false, 'message'=>'Invalid ID']);
            exit;
        }
        
        $sql = "UPDATE tblcourse_enrollment SET is_deleted = 1, deleted_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        echo json_encode(['success'=>true, 'message'=>'Enrollment deleted']);
    }
    
    else {
        echo json_encode(['success'=>false, 'message'=>'Method not allowed']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
}
?>
