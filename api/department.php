<?php
// CORS and JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	echo json_encode(['success' => true]);
	exit;
}

require_once __DIR__ . '/db.php';

function readJsonBody() {
	$raw = file_get_contents('php://input');
	if (!$raw) {
		return [];
	}
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
			$stmt = $pdo->prepare('SELECT dept_id, dept_code, dept_name FROM tbldepartment WHERE dept_id = ?');
			$stmt->execute([$id]);
			$dept = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$dept) {
				respond(['success' => false, 'message' => 'Department not found'], 404);
			}
			respond(['success' => true, 'data' => $dept]);
		} else {
			$stmt = $pdo->query('SELECT dept_id, dept_code, dept_name FROM tbldepartment ORDER BY dept_id DESC');
			$depts = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success' => true, 'data' => $depts]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$code = isset($body['dept_code']) ? trim($body['dept_code']) : '';
		$name = isset($body['dept_name']) ? trim($body['dept_name']) : '';

		if ($code === '' || $name === '') {
			respond(['success' => false, 'message' => 'Missing required fields: dept_code, dept_name'], 422);
		}

		$stmt = $pdo->prepare('INSERT INTO tbldepartment (dept_code, dept_name) VALUES (?, ?)');
		$stmt->execute([$code, $name]);
		$newId = intval($pdo->lastInsertId());

		respond(['success' => true, 'data' => ['dept_id' => $newId, 'dept_code' => $code, 'dept_name' => $name]], 201);
	}

	if ($method === 'PUT') {
		if (!$id) {
			respond(['success' => false, 'message' => 'Missing id'], 400);
		}
		$body = readJsonBody();
		$code = array_key_exists('dept_code', $body) ? trim((string)$body['dept_code']) : null;
		$name = array_key_exists('dept_name', $body) ? trim((string)$body['dept_name']) : null;

		$stmt = $pdo->prepare('SELECT dept_id, dept_code, dept_name FROM tbldepartment WHERE dept_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$existing) {
			respond(['success' => false, 'message' => 'Department not found'], 404);
		}

		$code = $code !== null ? $code : $existing['dept_code'];
		$name = $name !== null ? $name : $existing['dept_name'];

		$stmt = $pdo->prepare('UPDATE tbldepartment SET dept_code = ?, dept_name = ? WHERE dept_id = ?');
		$stmt->execute([$code, $name, $id]);

		respond(['success' => true, 'data' => ['dept_id' => $id, 'dept_code' => $code, 'dept_name' => $name]]);
	}

	if ($method === 'DELETE') {
		if (!$id) {
			respond(['success' => false, 'message' => 'Missing id'], 400);
		}
		respond(['success' => true, 'message' => 'Soft delete: department hidden in UI only']);
	}

	respond(['success' => false, 'message' => 'Method not allowed'], 405);
} catch (PDOException $e) {
	respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>


