<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $data = readJsonBody();
    
    // Validate required fields
    if (empty($data['student_no'])) {
        respond(['success' => false, 'message' => 'Student number is required'], 400);
    }
    
    $studentNo = trim($data['student_no']);
    
    // Search for student in tblstudent
    $stmt = $pdo->prepare('
        SELECT 
            s.student_id,
            s.student_no,
            s.first_name,
            s.last_name,
            s.middle_initial,
            s.email,
            s.gender,
            s.birthdate,
            s.year_level,
            s.program_id,
            p.program_code,
            p.program_name,
            CASE 
                WHEN s.password IS NOT NULL AND s.password != "" THEN true 
                ELSE false 
            END as has_account
        FROM tblstudent s
        LEFT JOIN tblprogram p ON s.program_id = p.program_id
        WHERE s.student_no = ? AND s.is_deleted = 0
    ');
    
    $stmt->execute([$studentNo]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        // Student found in database
        respond([
            'success' => true,
            'found' => true,
            'student' => $student,
            'message' => $student['has_account'] ? 'Student found with existing account' : 'Student found, can create account'
        ]);
    } else {
        // Student not found
        respond([
            'success' => true,
            'found' => false,
            'message' => 'Student number not found in database'
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Student lookup error: ' . $e->getMessage());
    respond(['success' => false, 'message' => 'Database error occurred'], 500);
} catch (Exception $e) {
    error_log('Student lookup error: ' . $e->getMessage());
    respond(['success' => false, 'message' => 'Lookup failed'], 500);
}
?>