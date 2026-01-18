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

// Simple router based on method and optional id in query string
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
	if ($method === 'GET') {
		if ($id) {
			$stmt = $pdo->prepare('SELECT course_id, course_code, course_title, units, year_level, lecture_hours, lab_hours, dept_id FROM tblcourse WHERE course_id = ? AND (is_deleted = 0 OR is_deleted IS NULL)');
			$stmt->execute([$id]);
			$course = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$course) {
				respond(['success' => false, 'message' => 'Course not found'], 404);
			}
			respond(['success' => true, 'data' => $course]);
		} else {
			$stmt = $pdo->query('SELECT course_id, course_code, course_title, units, year_level, lecture_hours, lab_hours, dept_id FROM tblcourse WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY year_level ASC, course_code ASC');
			$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
			respond(['success' => true, 'data' => $courses]);
		}
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$code = isset($body['code']) ? trim($body['code']) : '';
		$title = isset($body['title']) ? trim($body['title']) : '';
		$units = isset($body['units']) ? intval($body['units']) : 0;
		$lectureHours = isset($body['lecture_hours']) ? intval($body['lecture_hours']) : null;
		$labHours = isset($body['lab_hours']) ? intval($body['lab_hours']) : null;

		if ($code === '' || $title === '' || $units <= 0) {
			respond(['success' => false, 'message' => 'Missing required fields: code, title, units'], 422);
		}

		$deptId = isset($body['dept_id']) ? intval($body['dept_id']) : null;
		$stmt = $pdo->prepare('INSERT INTO tblcourse (course_code, course_title, units, lecture_hours, lab_hours, dept_id) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->execute([$code, $title, $units, $lectureHours, $labHours, $deptId]);
		$newId = intval($pdo->lastInsertId());

		respond(['success' => true, 'data' => ['id' => $newId, 'code' => $code, 'title' => $title, 'units' => $units, 'lecture_hours' => $lectureHours, 'lab_hours' => $labHours, 'dept_id' => $deptId]], 201);
	}

	if ($method === 'PUT') {
		if (!$id) {
			respond(['success' => false, 'message' => 'Missing id'], 400);
		}
		$body = readJsonBody();
		$code = isset($body['code']) ? trim($body['code']) : null;
		$title = isset($body['title']) ? trim($body['title']) : null;
		$units = isset($body['units']) ? intval($body['units']) : null;
		$lectureHours = array_key_exists('lecture_hours', $body) ? (is_null($body['lecture_hours']) || $body['lecture_hours'] === '' ? null : intval($body['lecture_hours'])) : null;
		$labHours = array_key_exists('lab_hours', $body) ? (is_null($body['lab_hours']) || $body['lab_hours'] === '' ? null : intval($body['lab_hours'])) : null;
		$deptId = array_key_exists('dept_id', $body) ? (is_null($body['dept_id']) || $body['dept_id'] === '' ? null : intval($body['dept_id'])) : null;

		// Fetch existing
		$stmt = $pdo->prepare('SELECT course_id, course_code, course_title, units, lecture_hours, lab_hours, dept_id FROM tblcourse WHERE course_id = ?');
		$stmt->execute([$id]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$existing) {
			respond(['success' => false, 'message' => 'Course not found'], 404);
		}

		$code = $code !== null ? $code : $existing['course_code'];
		$title = $title !== null ? $title : $existing['course_title'];
		$units = $units !== null ? $units : intval($existing['units']);
		$lectureHours = $lectureHours !== null ? $lectureHours : ($existing['lecture_hours'] !== null ? intval($existing['lecture_hours']) : null);
		$labHours = $labHours !== null ? $labHours : ($existing['lab_hours'] !== null ? intval($existing['lab_hours']) : null);
		$deptId = ($deptId === null) ? ($existing['dept_id'] !== null ? intval($existing['dept_id']) : null) : $deptId;

		$stmt = $pdo->prepare('UPDATE tblcourse SET course_code = ?, course_title = ?, units = ?, lecture_hours = ?, lab_hours = ?, dept_id = ? WHERE course_id = ?');
		$stmt->execute([$code, $title, $units, $lectureHours, $labHours, $deptId, $id]);

		respond(['success' => true, 'data' => ['id' => $id, 'code' => $code, 'title' => $title, 'units' => $units]]);
	}

	if ($method === 'DELETE') {
		if (!$id) {
			respond(['success' => false, 'message' => 'Missing id'], 400);
		}
		respond(['success' => true, 'message' => 'Soft delete: course hidden in UI only']);
	}

	respond(['success' => false, 'message' => 'Method not allowed'], 405);
} catch (PDOException $e) {
	respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>

