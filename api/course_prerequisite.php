<?php
// api/course_prerequisite.php - CRUD for course prerequisites (uses tblcourse/tblcourse_prerequisite)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { echo json_encode(['success'=>true]); exit; }

require_once __DIR__ . '/db.php'; // provides $pdo

function respond($data,$code=200){ http_response_code($code); echo json_encode($data); exit; }
function readJson(){ $r=file_get_contents('php://input'); $d=json_decode($r,true); return is_array($d)?$d:[]; }

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null; // composite like "<course_id>-<prereq_course_id>"

try {
    if ($method === 'GET') {
        // Synthetic id ensures UI can edit/delete even with composite PK
        $sql = "SELECT CONCAT(cp.course_id,'-',cp.prereq_course_id) AS id,
                       cp.course_id,
                       c1.course_code AS course_code,
                       c1.course_title AS course_name,
                       cp.prereq_course_id AS prerequisite_id,
                       c2.course_code AS prereq_code,
                       c2.course_title AS prereq_name
                FROM tblcourse_prerequisite cp
                JOIN tblcourse c1 ON cp.course_id = c1.course_id
                JOIN tblcourse c2 ON cp.prereq_course_id = c2.course_id
                ORDER BY cp.course_id DESC, cp.prereq_course_id DESC";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        respond(['success'=>true,'data'=>$rows]);
    }

    if ($method === 'POST') {
        $b = readJson();
        if (!isset($b['course_id'], $b['prerequisite_id'])) respond(['success'=>false,'message'=>'Missing fields'],422);
        $course_id = intval($b['course_id']);
        $prereq_id = intval($b['prerequisite_id']);
        if ($course_id===0 || $prereq_id===0) respond(['success'=>false,'message'=>'Invalid ids'],422);
        if ($course_id === $prereq_id) respond(['success'=>false,'message'=>'Course cannot be its own prerequisite'],422);
        // Upsert-like protection: avoid duplicates
        $exists = $pdo->prepare('SELECT 1 FROM tblcourse_prerequisite WHERE course_id=? AND prereq_course_id=?');
        $exists->execute([$course_id,$prereq_id]);
        if ($exists->fetch()) respond(['success'=>true]);
        $stmt = $pdo->prepare('INSERT INTO tblcourse_prerequisite (course_id, prereq_course_id) VALUES (?, ?)');
        $stmt->execute([$course_id, $prereq_id]);
        respond(['success'=>true],201);
    }

    if ($method === 'PUT') {
        if(!$id) respond(['success'=>false,'message'=>'Missing id'],400);
        $b = readJson();
        if (!isset($b['course_id'], $b['prerequisite_id'])) respond(['success'=>false,'message'=>'Missing fields'],422);
        $course_id = intval($b['course_id']);
        $prereq_id = intval($b['prerequisite_id']);
        if ($course_id === $prereq_id) respond(['success'=>false,'message'=>'Course cannot be its own prerequisite'],422);
        // Parse original pair from synthetic id
        if (!preg_match('/^(\d+)-(\d+)$/', $id, $m)) respond(['success'=>false,'message'=>'Bad id'],400);
        $old_course = intval($m[1]);
        $old_prereq = intval($m[2]);
        // If pair changed, delete old then insert new (since PK is composite)
        $pdo->beginTransaction();
        try {
            $del = $pdo->prepare('DELETE FROM tblcourse_prerequisite WHERE course_id=? AND prereq_course_id=?');
            $del->execute([$old_course,$old_prereq]);
            $ins = $pdo->prepare('INSERT INTO tblcourse_prerequisite (course_id, prereq_course_id) VALUES (?, ?)');
            $ins->execute([$course_id,$prereq_id]);
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        respond(['success'=>true]);
    }

    if ($method === 'DELETE') {
        if(!$id) respond(['success'=>false,'message'=>'Missing id'],400);
        // Soft delete: UI hides the item; DB remains unchanged.
        respond(['success'=>true,'message'=>'Soft delete: course prerequisite hidden in UI only']);
    }

    respond(['success'=>false,'message'=>'Invalid request'],405);
} catch (PDOException $e) {
    respond(['success'=>false,'message'=>'Database error: '.$e->getMessage()],500);
}
