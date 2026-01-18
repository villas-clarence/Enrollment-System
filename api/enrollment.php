<?php
// api/enrollment.php (PDO + correct schema)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { echo json_encode(['success'=>true]); exit; }

require_once __DIR__ . '/db.php'; // provides $pdo (PDO)

function readJsonBody(){ $raw=file_get_contents('php://input'); if(!$raw){ return []; } $d=json_decode($raw,true); return is_array($d)?$d:[]; }
function respond($data,$code=200){ http_response_code($code); echo json_encode($data); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    if ($method === 'GET') {
        // Return all students with their complete info and enrollment status
        $sql = "SELECT COALESCE(e.enrollment_id, s.student_id) as enrollment_id, 
                       s.student_id, s.student_no, s.last_name, s.first_name, s.middle_name, 
                       s.email, s.gender, s.birthdate, s.year_level, s.program_id,
                       e.section_id, sec.section_code AS section_name, 
                       COALESCE(e.enrollment_type, 'Regular') as enrollment_type,
                       COALESCE(e.date_enrolled, CURDATE()) as date_enrolled, 
                       COALESCE(e.status, 'Active') as status, 
                       e.letter_grade
                FROM tblstudent s
                LEFT JOIN tblenrollment e ON s.student_id = e.student_id
                LEFT JOIN tblsection sec ON e.section_id = sec.section_id
                WHERE s.is_deleted = 0
                ORDER BY s.student_id ASC";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(['success'=>true,'data'=>$rows]);
    }

    if ($method === 'POST') {
        $b = readJsonBody();
        $student_id = isset($b['student_id']) ? intval($b['student_id']) : null;
        $section_id = isset($b['section_id']) ? intval($b['section_id']) : null;
        $enrollment_type = isset($b['enrollment_type']) ? trim($b['enrollment_type']) : 'Regular';
        $courses = isset($b['courses']) && is_array($b['courses']) ? $b['courses'] : [];
        $date_enrolled = isset($b['date_enrolled']) ? trim($b['date_enrolled']) : null; // YYYY-MM-DD
        $status = isset($b['status']) ? trim($b['status']) : '';
        $letter = isset($b['letter_grade']) ? trim($b['letter_grade']) : null;

        if ($student_id===null || $section_id===null || $date_enrolled===null || $status==='') {
            respond(['success'=>false,'message'=>'Missing required fields: student_id, section_id, date_enrolled, status'],422);
        }

        try {
            $pdo->beginTransaction();
            
            // Insert enrollment
            $stmt = $pdo->prepare('INSERT INTO tblenrollment (student_id, section_id, enrollment_type, date_enrolled, status, letter_grade) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$student_id, $section_id, $enrollment_type, $date_enrolled, $status, $letter]);
            $newId = intval($pdo->lastInsertId());
            
            // Insert course enrollments if this is an irregular enrollment
            if ($enrollment_type === 'Irregular' && !empty($courses)) {
                $stmt = $pdo->prepare('INSERT INTO tblenrollment_courses (enrollment_id, course_id) VALUES (?, ?)');
                foreach ($courses as $courseId) {
                    $stmt->execute([$newId, intval($courseId)]);
                }
            }
            
            $pdo->commit();
            respond(['success'=>true,'data'=>['enrollment_id'=>$newId]],201);
        } catch (Exception $e) {
            $pdo->rollBack();
            respond(['success'=>false,'message'=>'Failed to save enrollment: ' . $e->getMessage()], 500);
        }
    }

    if ($method === 'PUT') {
        if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
        $b = readJsonBody();
        
        // Get existing enrollment
        $stmt = $pdo->prepare('SELECT * FROM tblenrollment WHERE enrollment_id = ?');
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$existing){ respond(['success'=>false,'message'=>'Enrollment not found'],404); }

        $student_id = array_key_exists('student_id',$b) ? ( $b['student_id']===null ? null : intval($b['student_id']) ) : intval($existing['student_id']);
        $section_id = array_key_exists('section_id',$b) ? ( $b['section_id']===null ? null : intval($b['section_id']) ) : intval($existing['section_id']);
        $enrollment_type = 'Regular'; // Force Regular enrollment type since we don't have the courses table
        $date_enrolled = array_key_exists('date_enrolled',$b) ? ( $b['date_enrolled']===null ? null : trim((string)$b['date_enrolled']) ) : $existing['date_enrolled'];
        $status = array_key_exists('status',$b) ? trim((string)$b['status']) : ($existing['status'] ?? 'Enrolled');
        $letter = array_key_exists('letter_grade',$b) ? ( $b['letter_grade']===null ? null : trim((string)$b['letter_grade']) ) : $existing['letter_grade'];

        try {
            $pdo->beginTransaction();
            
            // Update enrollment
            $stmt = $pdo->prepare('UPDATE tblenrollment SET student_id = ?, section_id = ?, enrollment_type = ?, date_enrolled = ?, status = ?, letter_grade = ? WHERE enrollment_id = ?');
            $stmt->execute([$student_id, $section_id, $enrollment_type, $date_enrolled, $status, $letter, $id]);
            
            // Commit the transaction
            $pdo->commit();
            respond(['success'=>true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            respond(['success'=>false,'message'=>'Failed to update enrollment: ' . $e->getMessage()], 500);
        }
    }

    if ($method === 'DELETE') {
        if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
        try {
            $pdo->beginTransaction();
            
            // Delete course associations first (due to foreign key constraint)
            $stmt = $pdo->prepare('DELETE FROM tblenrollment_courses WHERE enrollment_id = ?');
            $stmt->execute([$id]);
            
            // Then delete the enrollment
            $stmt = $pdo->prepare('DELETE FROM tblenrollment WHERE enrollment_id = ?');
            $stmt->execute([$id]);
            
            $pdo->commit();
            respond(['success'=>true,'message'=>'Enrollment and associated courses deleted']);
        } catch (Exception $e) {
            $pdo->rollBack();
            respond(['success'=>false,'message'=>'Failed to delete enrollment: ' . $e->getMessage()], 500);
        }
    }

    respond(['success'=>false,'message'=>'Invalid request'],405);
} catch (PDOException $e) {
    respond(['success'=>false,'message'=>'Database error: '.$e->getMessage()],500);
}
