<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/db.php';

function readJsonBody() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function authenticateUser($username, $password, $role, $pdo) {
    switch($role) {
        case 'student':
            // Authenticate against tblstudent using student_no as username
            $stmt = $pdo->prepare('SELECT student_id, student_no, password, first_name, last_name FROM tblstudent WHERE student_no = ? AND is_deleted = 0');
            $stmt->execute([$username]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) return false;
            
            // Check password (if no password set, use default "student123")
            if (empty($student['password'])) {
                // Default password for students without password set
                return $password === 'student123' ? $student['student_id'] : false;
            }
            
            // Verify hashed password
            return password_verify($password, $student['password']) ? $student['student_id'] : false;
            
        case 'faculty':
            // Authenticate against tblinstructor using employee_id or email
            $stmt = $pdo->prepare('
                SELECT instructor_id, employee_id, email, password, first_name, last_name 
                FROM tblinstructor 
                WHERE (employee_id = ? OR email = ?) AND is_deleted = 0
            ');
            $stmt->execute([$username, $username]);
            $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$faculty) return false;
            
            // Check password (if no password set, use default "faculty123")
            if (empty($faculty['password'])) {
                return $password === 'faculty123' ? $faculty['instructor_id'] : false;
            }
            
            return password_verify($password, $faculty['password']) ? $faculty['instructor_id'] : false;
            
        case 'admin':
            // Use hardcoded admin credentials since tbladmin table doesn't exist
            if ($username === 'admin' && $password === 'admin123') {
                return 1; // Default admin ID
            }
            return false;
    }
    
    return false;
}

function getUserInfo($role, $userId, $pdo) {
    switch($role) {
        case 'student':
            $stmt = $pdo->prepare('
                SELECT s.*, p.program_name, p.program_code, d.dept_name 
                FROM tblstudent s 
                JOIN tblprogram p ON s.program_id = p.program_id 
                JOIN tbldepartment d ON p.dept_id = d.dept_id 
                WHERE s.student_id = ?
            ');
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        case 'faculty':
            $stmt = $pdo->prepare('
                SELECT i.*, d.dept_name, d.dept_code 
                FROM tblinstructor i 
                JOIN tbldepartment d ON i.dept_id = d.dept_id 
                WHERE i.instructor_id = ?
            ');
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        case 'admin':
            return [
                'user_id' => $userId,
                'name' => 'System Administrator',
                'role' => 'admin',
                'dept_name' => 'Administration'
            ];
    }
    return null;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $body = readJsonBody();
        $username = isset($body['username']) ? trim($body['username']) : '';
        $password = isset($body['password']) ? trim($body['password']) : '';
        $role = isset($body['role']) ? trim($body['role']) : '';
        
        if (empty($username) || empty($password) || empty($role)) {
            respond(['success' => false, 'message' => 'Missing credentials'], 400);
        }
        
        $userId = authenticateUser($username, $password, $role, $pdo);
        if (!$userId) {
            respond(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        
        $userInfo = getUserInfo($role, $userId, $pdo);
        if (!$userInfo) {
            respond(['success' => false, 'message' => 'User not found'], 404);
        }
        
        // Generate session token (in production, use proper JWT or session management)
        $sessionToken = base64_encode($role . ':' . $userId . ':' . time());
        
        respond([
            'success' => true,
            'data' => [
                'token' => $sessionToken,
                'role' => $role,
                'user_id' => $userId,
                'user_info' => $userInfo
            ]
        ]);
    }
    
    if ($method === 'GET') {
        // Validate session token
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (empty($authHeader)) {
            respond(['success' => false, 'message' => 'No authorization header'], 401);
        }
        
        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);
        
        if (count($parts) !== 3) {
            respond(['success' => false, 'message' => 'Invalid token'], 401);
        }
        
        list($role, $userId, $timestamp) = $parts;
        
        // Check if token is not too old (24 hours)
        if (time() - intval($timestamp) > 86400) {
            respond(['success' => false, 'message' => 'Token expired'], 401);
        }
        
        $userInfo = getUserInfo($role, $userId, $pdo);
        if (!$userInfo) {
            respond(['success' => false, 'message' => 'User not found'], 404);
        }
        
        respond([
            'success' => true,
            'data' => [
                'role' => $role,
                'user_id' => $userId,
                'user_info' => $userInfo
            ]
        ]);
    }
    
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
    
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>