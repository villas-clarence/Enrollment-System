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

function readJsonBody() {
	$raw = file_get_contents('php://input');
	if (!$raw) { return []; }
	$decoded = json_decode($raw, true);
	return is_array($decoded) ? $decoded : [];
}

function respond($data, $statusCode = 200) {
	http_response_code($statusCode);
	echo json_encode($data);
	exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
	if ($method === 'GET') {
		if ($id) {
			$stmt = $pdo->prepare('SELECT term_id, term_code, start_date, end_date FROM tblterm WHERE term_id = ?');
			$stmt->execute([$id]);
			$term = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$term) { respond(['success' => false, 'message' => 'Term not found'], 404); }
			respond(['success' => true, 'data' => $term]);
		} else {
			$stmt = $pdo->query('SELECT term_id, term_code, start_date, end_date FROM tblterm ORDER BY term_id DESC');
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success' => true, 'data' => $list]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$code = isset($body['term_code']) ? trim($body['term_code']) : '';
		$start = isset($body['start_date']) ? trim($body['start_date']) : null;
		$end = isset($body['end_date']) ? trim($body['end_date']) : null;

		if ($code === '') { respond(['success' => false, 'message' => 'Missing required field: term_code'], 422); }

		$stmt = $pdo->prepare('INSERT INTO tblterm (term_code, start_date, end_date) VALUES (?, ?, ?)');
		$stmt->execute([$code, $start, $end]);
		$newId = intval($pdo->lastInsertId());
		respond(['success' => true, 'data' => ['term_id' => $newId, 'term_code' => $code, 'start_date' => $start, 'end_date' => $end]], 201);
	}

	if ($method === 'PUT') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		$body = readJsonBody();
		$code = array_key_exists('term_code', $body) ? trim((string)$body['term_code']) : null;
		$start = array_key_exists('start_date', $body) ? ( $body['start_date'] === '' ? null : trim((string)$body['start_date']) ) : null;
		$end = array_key_exists('end_date', $body) ? ( $body['end_date'] === '' ? null : trim((string)$body['end_date']) ) : null;

		$stmt = $pdo->prepare('SELECT term_id, term_code, start_date, end_date FROM tblterm WHERE term_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$existing) { respond(['success' => false, 'message' => 'Term not found'], 404); }

		$code = $code !== null ? $code : $existing['term_code'];
		$start = $start !== null ? $start : $existing['start_date'];
		$end = $end !== null ? $end : $existing['end_date'];

		$stmt = $pdo->prepare('UPDATE tblterm SET term_code = ?, start_date = ?, end_date = ? WHERE term_id = ?');
		$stmt->execute([$code, $start, $end, $id]);
		respond(['success' => true, 'data' => ['term_id' => $id, 'term_code' => $code, 'start_date' => $start, 'end_date' => $end]]);
	}

	if ($method === 'DELETE') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		respond(['success' => true, 'message' => 'Soft delete: term hidden in UI only']);
	}

	respond(['success' => false, 'message' => 'Method not allowed'], 405);
} catch (PDOException $e) {
	respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>







