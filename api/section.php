<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { echo json_encode(['success'=>true]); exit; }

require_once __DIR__ . '/db.php';

function readJsonBody(){ $raw=file_get_contents('php://input'); if(!$raw){ return []; } $d=json_decode($raw,true); return is_array($d)?$d:[]; }
function respond($data,$code=200){ http_response_code($code); echo json_encode($data); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    if ($method === 'GET') {
        if ($id) {
            $sql = "SELECT s.section_id, s.section_code, s.course_id, s.term_id, s.instructor_id, s.day_pattern, s.start_time, s.end_time, s.room_id, s.max_capacity,
                           c.course_code, c.course_title,
                           i.first_name as instructor_first_name, i.last_name as instructor_last_name,
                           r.room_code, r.building,
                           t.term_code
                    FROM tblsection s
                    LEFT JOIN tblcourse c ON s.course_id = c.course_id
                    LEFT JOIN tblinstructor i ON s.instructor_id = i.instructor_id
                    LEFT JOIN tblroom r ON s.room_id = r.room_id
                    LEFT JOIN tblterm t ON s.term_id = t.term_id
                    WHERE s.section_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row){ respond(['success'=>false,'message'=>'Section not found'],404); }
            respond(['success'=>true,'data'=>$row]);
        } else {
            $sql = "SELECT s.section_id, s.section_code, s.course_id, s.term_id, s.instructor_id, s.day_pattern, s.start_time, s.end_time, s.room_id, s.max_capacity,
                           c.course_code, c.course_title,
                           i.first_name as instructor_first_name, i.last_name as instructor_last_name,
                           r.room_code, r.building,
                           t.term_code
                    FROM tblsection s
                    LEFT JOIN tblcourse c ON s.course_id = c.course_id
                    LEFT JOIN tblinstructor i ON s.instructor_id = i.instructor_id
                    LEFT JOIN tblroom r ON s.room_id = r.room_id
                    LEFT JOIN tblterm t ON s.term_id = t.term_id
                    ORDER BY s.section_code ASC, s.section_id DESC";
            $stmt = $pdo->query($sql);
            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(['success'=>true,'data'=>$list]);
        }
    }

    if ($method === 'POST') {
        $b = readJsonBody();
        $section_code = isset($b['section_code']) ? trim($b['section_code']) : '';
        $course_id = isset($b['course_id']) && $b['course_id'] !== '' ? intval($b['course_id']) : null;
        $term_id = isset($b['term_id']) && $b['term_id'] !== '' ? intval($b['term_id']) : null;
        $instructor_id = isset($b['instructor_id']) && $b['instructor_id'] !== '' ? intval($b['instructor_id']) : null;
        $day_pattern = isset($b['day_pattern']) && $b['day_pattern'] !== '' ? trim($b['day_pattern']) : null;
        $start_time = isset($b['start_time']) && $b['start_time'] !== '' ? trim($b['start_time']) : null;
        $end_time = isset($b['end_time']) && $b['end_time'] !== '' ? trim($b['end_time']) : null;
        $room_id = isset($b['room_id']) && $b['room_id'] !== '' ? intval($b['room_id']) : null;
        $max_capacity = isset($b['max_capacity']) && $b['max_capacity'] !== '' ? intval($b['max_capacity']) : 40; // Default to 40

        if ($section_code === '') { respond(['success'=>false,'message'=>'Missing required field: section_code'],422); }

        $stmt = $pdo->prepare('INSERT INTO tblsection (section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity]);
        $newId = intval($pdo->lastInsertId());
        respond(['success'=>true,'data'=>[
            'section_id'=>$newId,
            'section_code'=>$section_code,
            'course_id'=>$course_id,
            'term_id'=>$term_id,
            'instructor_id'=>$instructor_id,
            'day_pattern'=>$day_pattern,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'room_id'=>$room_id,
            'max_capacity'=>$max_capacity
        ]],201);
    }

    if ($method === 'PUT') {
        if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
        $b = readJsonBody();
        // Fetch existing
        $stmt = $pdo->prepare('SELECT section_id, section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity FROM tblsection WHERE section_id = ?');
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$existing){ respond(['success'=>false,'message'=>'Section not found'],404); }

        $section_code = array_key_exists('section_code',$b) ? trim((string)$b['section_code']) : $existing['section_code'];
        $course_id = array_key_exists('course_id',$b) ? ($b['course_id'] === '' || $b['course_id'] === null ? null : intval($b['course_id'])) : $existing['course_id'];
        $term_id = array_key_exists('term_id',$b) ? ($b['term_id'] === '' || $b['term_id'] === null ? null : intval($b['term_id'])) : $existing['term_id'];
        $instructor_id = array_key_exists('instructor_id',$b) ? ($b['instructor_id'] === '' || $b['instructor_id'] === null ? null : intval($b['instructor_id'])) : $existing['instructor_id'];
        $day_pattern = array_key_exists('day_pattern',$b) ? ($b['day_pattern'] === '' || $b['day_pattern'] === null ? null : trim((string)$b['day_pattern'])) : $existing['day_pattern'];
        $start_time = array_key_exists('start_time',$b) ? ($b['start_time'] === '' || $b['start_time'] === null ? null : trim((string)$b['start_time'])) : $existing['start_time'];
        $end_time = array_key_exists('end_time',$b) ? ($b['end_time'] === '' || $b['end_time'] === null ? null : trim((string)$b['end_time'])) : $existing['end_time'];
        $room_id = array_key_exists('room_id',$b) ? ($b['room_id'] === '' || $b['room_id'] === null ? null : intval($b['room_id'])) : $existing['room_id'];
        $max_capacity = array_key_exists('max_capacity',$b) ? ($b['max_capacity'] === '' || $b['max_capacity'] === null ? 40 : intval($b['max_capacity'])) : ($existing['max_capacity'] ?? 40);

        $stmt = $pdo->prepare('UPDATE tblsection SET section_code = ?, course_id = ?, term_id = ?, instructor_id = ?, day_pattern = ?, start_time = ?, end_time = ?, room_id = ?, max_capacity = ? WHERE section_id = ?');
        $stmt->execute([$section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity, $id]);
        respond(['success'=>true,'data'=>[
            'section_id'=>$id,
            'section_code'=>$section_code,
            'course_id'=>$course_id,
            'term_id'=>$term_id,
            'instructor_id'=>$instructor_id,
            'day_pattern'=>$day_pattern,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'room_id'=>$room_id,
            'max_capacity'=>$max_capacity
        ]]);
    }

    if ($method === 'DELETE') {
        if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
        
        // Check if section exists
        $stmt = $pdo->prepare('SELECT section_id FROM tblsection WHERE section_id = ?');
        $stmt->execute([$id]);
        if(!$stmt->fetch()){ respond(['success'=>false,'message'=>'Section not found'],404); }
        
        // First, delete related enrollments
        try {
            $stmt = $pdo->prepare('DELETE FROM tblenrollment WHERE section_id = ?');
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Try soft delete if hard delete fails
            try {
                $stmt = $pdo->prepare('UPDATE tblenrollment SET is_deleted = 1 WHERE section_id = ?');
                $stmt->execute([$id]);
            } catch (PDOException $e2) {
                // Ignore if no enrollments or column doesn't exist
            }
        }
        
        // Now delete the section
        try {
            $stmt = $pdo->prepare('DELETE FROM tblsection WHERE section_id = ?');
            $stmt->execute([$id]);
            respond(['success'=>true,'message'=>'Section deleted successfully']);
        } catch (PDOException $e) {
            respond(['success'=>false,'message'=>'Cannot delete section: ' . $e->getMessage()],500);
        }
    }

    respond(['success'=>false,'message'=>'Method not allowed'],405);
} catch (PDOException $e) {
    respond(['success'=>false,'message'=>'Database error: '.$e->getMessage()],500);
}
