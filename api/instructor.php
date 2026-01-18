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
			$stmt = $pdo->prepare('
				SELECT 
					i.instructor_id, 
					i.last_name, 
					i.first_name, 
					i.email, 
					i.dept_id,
					i.employee_id,
					i.position,
					i.specialization,
					i.password,
					i.is_deleted,
					d.dept_name,
					d.dept_code
				FROM tblinstructor i
				LEFT JOIN tbldepartment d ON i.dept_id = d.dept_id
				WHERE i.instructor_id = ? AND i.is_deleted = 0
			');
			$stmt->execute([$id]);
			$inst = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$inst) { respond(['success' => false, 'message' => 'Instructor not found'], 404); }
			respond(['success' => true, 'data' => $inst]);
		} else {
			$stmt = $pdo->query('
				SELECT 
					i.instructor_id, 
					i.last_name, 
					i.first_name, 
					i.email, 
					i.dept_id,
					i.employee_id,
					i.position,
					i.specialization,
					i.password,
					i.is_deleted,
					d.dept_name,
					d.dept_code
				FROM tblinstructor i
				LEFT JOIN tbldepartment d ON i.dept_id = d.dept_id
				WHERE i.is_deleted = 0
				ORDER BY d.dept_name, i.last_name, i.first_name
			');
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success' => true, 'data' => $list]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$last = isset($body['last_name']) ? trim($body['last_name']) : '';
		$first = isset($body['first_name']) ? trim($body['first_name']) : '';
		$email = isset($body['email']) ? trim($body['email']) : '';
		$deptId = isset($body['dept_id']) ? intval($body['dept_id']) : null;

		if ($last === '' || $first === '' || $email === '') {
			respond(['success' => false, 'message' => 'Missing required fields: last_name, first_name, email'], 422);
		}

		$stmt = $pdo->prepare('INSERT INTO tblinstructor (last_name, first_name, email, dept_id) VALUES (?, ?, ?, ?)');
		$stmt->execute([$last, $first, $email, $deptId]);
		$newId = intval($pdo->lastInsertId());

		respond(['success' => true, 'data' => ['instructor_id' => $newId, 'last_name' => $last, 'first_name' => $first, 'email' => $email, 'dept_id' => $deptId]], 201);
	}

	if ($method === 'PUT') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		$body = readJsonBody();
		$last = array_key_exists('last_name', $body) ? trim((string)$body['last_name']) : null;
		$first = array_key_exists('first_name', $body) ? trim((string)$body['first_name']) : null;
		$email = array_key_exists('email', $body) ? trim((string)$body['email']) : null;
		$deptId = array_key_exists('dept_id', $body) ? intval($body['dept_id']) : null;

		$stmt = $pdo->prepare('SELECT instructor_id, last_name, first_name, email, dept_id FROM tblinstructor WHERE instructor_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$existing) { respond(['success' => false, 'message' => 'Instructor not found'], 404); }

		$last = $last !== null ? $last : $existing['last_name'];
		$first = $first !== null ? $first : $existing['first_name'];
		$email = $email !== null ? $email : $existing['email'];
		$deptId = $deptId !== null ? $deptId : ($existing['dept_id'] !== null ? intval($existing['dept_id']) : null);

		$stmt = $pdo->prepare('UPDATE tblinstructor SET last_name = ?, first_name = ?, email = ?, dept_id = ? WHERE instructor_id = ?');
		$stmt->execute([$last, $first, $email, $deptId, $id]);

		respond(['success' => true, 'data' => ['instructor_id' => $id, 'last_name' => $last, 'first_name' => $first, 'email' => $email, 'dept_id' => $deptId]]);
	}

	if ($method === 'DELETE') {
		if (!$id) { respond(['success' => false, 'message' => 'Missing id'], 400); }
		
		// Soft delete - set is_deleted = 1
		$stmt = $pdo->prepare('UPDATE tblinstructor SET is_deleted = 1 WHERE instructor_id = ?');
		$stmt->execute([$id]);
		
		if ($stmt->rowCount() > 0) {
			respond(['success' => true, 'message' => 'Instructor deleted successfully']);
		} else {
			respond(['success' => false, 'message' => 'Instructor not found'], 404);
		}
	}

	respond(['success' => false, 'message' => 'Method not allowed'], 405);
} catch (PDOException $e) {
	respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>


