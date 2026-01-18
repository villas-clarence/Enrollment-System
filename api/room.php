<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	echo json_encode(['success' => true]);
	exit;
}

require_once __DIR__ . '/db.php';

function readJsonBody() { $raw = file_get_contents('php://input'); if(!$raw){ return []; } $d=json_decode($raw,true); return is_array($d)?$d:[]; }
function respond($data,$code=200){ http_response_code($code); echo json_encode($data); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
	if ($method === 'GET') {
		if ($id) {
			$stmt = $pdo->prepare('SELECT room_id, building, room_code, capacity FROM tblroom WHERE room_id = ?');
			$stmt->execute([$id]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row){ respond(['success'=>false,'message'=>'Room not found'],404); }
			respond(['success'=>true,'data'=>$row]);
		} else {
			$stmt = $pdo->query('SELECT room_id, building, room_code, capacity FROM tblroom ORDER BY room_id DESC');
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success'=>true,'data'=>$list]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$building = isset($body['building']) ? trim($body['building']) : '';
		$roomCode = isset($body['room_code']) ? trim($body['room_code']) : '';
		$capacity = isset($body['capacity']) ? intval($body['capacity']) : null;
		if ($building === '' || $roomCode === '') { respond(['success'=>false,'message'=>'Missing required fields: building, room_code'],422); }
		$stmt = $pdo->prepare('INSERT INTO tblroom (building, room_code, capacity) VALUES (?, ?, ?)');
		$stmt->execute([$building, $roomCode, $capacity]);
		$newId = intval($pdo->lastInsertId());
		respond(['success'=>true,'data'=>['room_id'=>$newId,'building'=>$building,'room_code'=>$roomCode,'capacity'=>$capacity]],201);
	}

	if ($method === 'PUT') {
		if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
		$body = readJsonBody();
		$building = array_key_exists('building',$body) ? trim((string)$body['building']) : null;
		$roomCode = array_key_exists('room_code',$body) ? trim((string)$body['room_code']) : null;
		$capacity = array_key_exists('capacity',$body) ? ( $body['capacity']===null ? null : intval($body['capacity']) ) : null;
		$stmt = $pdo->prepare('SELECT room_id, building, room_code, capacity FROM tblroom WHERE room_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$existing){ respond(['success'=>false,'message'=>'Room not found'],404); }
		$building = $building!==null ? $building : $existing['building'];
		$roomCode = $roomCode!==null ? $roomCode : $existing['room_code'];
		$capacity = $capacity!==null ? $capacity : ($existing['capacity']!==null ? intval($existing['capacity']) : null);
		$stmt = $pdo->prepare('UPDATE tblroom SET building = ?, room_code = ?, capacity = ? WHERE room_id = ?');
		$stmt->execute([$building, $roomCode, $capacity, $id]);
		respond(['success'=>true,'data'=>['room_id'=>$id,'building'=>$building,'room_code'=>$roomCode,'capacity'=>$capacity]]);
	}

	if ($method === 'DELETE') {
		if(!$id){ respond(['success'=>false,'message'=>'Missing id'],400); }
		respond(['success'=>true,'message'=>'Soft delete: room hidden in UI only']);
	}

	respond(['success'=>false,'message'=>'Method not allowed'],405);
} catch (PDOException $e) {
	respond(['success'=>false,'message'=>'Database error: '.$e->getMessage()],500);
}
?>


