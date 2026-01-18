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
			$stmt = $pdo->prepare('SELECT p.program_id, p.program_code, p.program_name, p.dept_id,
			                              d.dept_code, d.dept_name
			                       FROM tblprogram p
			                       LEFT JOIN tbldepartment d ON p.dept_id = d.dept_id
			                       WHERE p.program_id = ?');
			$stmt->execute([$id]);
			$program = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$program) {
				respond(['success' => false, 'message' => 'Program not found'], 404);
			}
			respond(['success' => true, 'data' => $program]);
		} else {
			$stmt = $pdo->query('SELECT p.program_id, p.program_code, p.program_name, p.dept_id,
			                           d.dept_code, d.dept_name
			                    FROM tblprogram p
			                    LEFT JOIN tbldepartment d ON p.dept_id = d.dept_id
			                    ORDER BY p.program_id DESC');
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success' => true, 'data' => $list]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$code = isset($body['program_code']) ? trim($body['program_code']) : '';
		$name = isset($body['program_name']) ? trim($body['program_name']) : '';
		$deptId = isset($body['dept_id']) ? intval($body['dept_id']) : null;

		if ($code === '' || $name === '') {
			respond(['success' => false, 'message' => 'Missing required fields: program_code, program_name'], 422);
		}

		$stmt = $pdo->prepare('INSERT INTO tblprogram (program_code, program_name, dept_id) VALUES (?, ?, ?)');
		$stmt->execute([$code, $name, $deptId]);
		$newId = intval($pdo->lastInsertId());

		respond(['success' => true, 'data' => ['program_id' => $newId, 'program_code' => $code, 'program_name' => $name, 'dept_id' => $deptId]], 201);
	}

	if ($method === 'PUT') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		$body = readJsonBody();
		$code = array_key_exists('program_code', $body) ? trim((string)$body['program_code']) : null;
		$name = array_key_exists('program_name', $body) ? trim((string)$body['program_name']) : null;
		$deptId = array_key_exists('dept_id', $body) ? intval($body['dept_id']) : null;

		$stmt = $pdo->prepare('SELECT program_id, program_code, program_name, dept_id FROM tblprogram WHERE program_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$existing) { respond(['success' => false, 'message' => 'Program not found'], 404); }

		$code = $code !== null ? $code : $existing['program_code'];
		$name = $name !== null ? $name : $existing['program_name'];
		$deptId = $deptId !== null ? $deptId : ($existing['dept_id'] !== null ? intval($existing['dept_id']) : null);

		$stmt = $pdo->prepare('UPDATE tblprogram SET program_code = ?, program_name = ?, dept_id = ? WHERE program_id = ?');
		$stmt->execute([$code, $name, $deptId, $id]);

		respond(['success' => true, 'data' => ['program_id' => $id, 'program_code' => $code, 'program_name' => $name, 'dept_id' => $deptId]]);
	}

	if ($method === 'DELETE') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		respond(['success' => true, 'message' => 'Soft delete: program hidden in UI only']);
	}

	respond(['success' => false, 'message' => 'Method not allowed'], 405);
} catch (PDOException $e) {
	respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>


