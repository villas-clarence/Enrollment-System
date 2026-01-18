<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $data = readJsonBody();
    
    // Debug: log the received data
    error_log('Registration data received: ' . json_encode($data));
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'password', 'role'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            respond(['success' => false, 'message' => "Missing required field: $field"], 400);
        }
    }
    
    $role = $data['role'];
    $email = trim($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $firstName = trim($data['first_name']);
    $lastName = trim($data['last_name']);
    
    if ($role === 'student') {
        // Check if this is for existing student or new student
        if (isset($data['existing_student']) && $data['existing_student'] === true) {
            // For existing student - just update password
            if (empty($data['student_no'])) {
                respond(['success' => false, 'message' => 'Student number is required'], 400);
            }
            
            $studentNo = trim($data['student_no']);
            
            // Check if student exists and doesn't have account yet
            $stmt = $pdo->prepare('SELECT student_id, first_name, last_name, password FROM tblstudent WHERE student_no = ? AND is_deleted = 0');
            $stmt->execute([$studentNo]);
            $existingStudent = $stmt->fetch();
            
            if (!$existingStudent) {
                respond(['success' => false, 'message' => 'Student not found in database'], 404);
            }
            
            // Check if student already has password
            if (!empty($existingStudent['password'])) {
                respond(['success' => false, 'message' => 'Student already has an account'], 409);
            }
            
            // Check if email is used by OTHER students
            $stmt = $pdo->prepare('SELECT email FROM tblstudent WHERE email = ? AND student_no != ? AND is_deleted = 0');
            $stmt->execute([$email, $studentNo]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already used by another student'], 409);
            }
            
            // Check if email exists in instructor or admin tables
            $stmt = $pdo->prepare('SELECT email FROM tblinstructor WHERE email = ? AND is_deleted = 0');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            // Update password and email for existing student
            $stmt = $pdo->prepare('UPDATE tblstudent SET password = ?, email = ? WHERE student_no = ?');
            $result = $stmt->execute([$password, $email, $studentNo]);
            
            if ($result) {
                respond(['success' => true, 'message' => 'Account created successfully for existing student', 'user_id' => $studentNo]);
            } else {
                respond(['success' => false, 'message' => 'Failed to create account'], 500);
            }
            
        } else {
            // For new student - full registration (original logic)
            if (empty($data['student_id']) || empty($data['program_id']) || empty($data['year_level'])) {
                respond(['success' => false, 'message' => 'Missing required student fields'], 400);
            }
            
            $studentId = trim($data['student_id']);
            $programId = intval($data['program_id']);
            $yearLevel = intval($data['year_level']);
            $middleName = isset($data['middle_name']) ? trim($data['middle_name']) : null;
            $birthday = isset($data['birthday']) ? trim($data['birthday']) : null;
            $gender = isset($data['gender']) ? trim($data['gender']) : null;
            
            // Check if student ID already exists
            $stmt = $pdo->prepare('SELECT student_no FROM tblstudent WHERE student_no = ?');
            $stmt->execute([$studentId]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Student ID already exists'], 409);
            }
            
            // Check if email exists anywhere
            $stmt = $pdo->prepare('SELECT email FROM tblstudent WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            $stmt = $pdo->prepare('SELECT email FROM tblinstructor WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            // Insert new student
            $stmt = $pdo->prepare('
                INSERT INTO tblstudent (student_no, first_name, last_name, middle_name, email, birthdate, gender, program_id, year_level, password, is_deleted, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
            ');
            $result = $stmt->execute([$studentId, $firstName, $lastName, $middleName, $email, $birthday, $gender, $programId, $yearLevel, $password]);
            
            if ($result) {
                respond(['success' => true, 'message' => 'Student account created successfully', 'user_id' => $studentId]);
            } else {
                respond(['success' => false, 'message' => 'Failed to create student account'], 500);
            }
        }
    } else if ($role === 'faculty') {
        // Check if this is for existing faculty or new faculty
        if (isset($data['existing_faculty']) && $data['existing_faculty'] === true) {
            // For existing faculty - just update password
            if (empty($data['instructor_id'])) {
                respond(['success' => false, 'message' => 'Instructor ID is required'], 400);
            }
            
            $instructorId = intval($data['instructor_id']);
            
            // Check if faculty exists and doesn't have account yet
            $stmt = $pdo->prepare('SELECT instructor_id, first_name, last_name, password FROM tblinstructor WHERE instructor_id = ? AND is_deleted = 0');
            $stmt->execute([$instructorId]);
            $existingFaculty = $stmt->fetch();
            
            if (!$existingFaculty) {
                respond(['success' => false, 'message' => 'Faculty not found in database'], 404);
            }
            
            // Check if faculty already has password
            if (!empty($existingFaculty['password'])) {
                respond(['success' => false, 'message' => 'Faculty already has an account'], 409);
            }
            
            // Check if email is used by OTHER faculty
            $stmt = $pdo->prepare('SELECT email FROM tblinstructor WHERE email = ? AND instructor_id != ? AND is_deleted = 0');
            $stmt->execute([$email, $instructorId]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already used by another faculty member'], 409);
            }
            
            // Check if email exists in student table
            $stmt = $pdo->prepare('SELECT email FROM tblstudent WHERE email = ? AND is_deleted = 0');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            // Update password and email for existing faculty
            $stmt = $pdo->prepare('UPDATE tblinstructor SET password = ?, email = ? WHERE instructor_id = ?');
            $result = $stmt->execute([$password, $email, $instructorId]);
            
            if ($result) {
                respond(['success' => true, 'message' => 'Account created successfully for existing faculty', 'user_id' => $data['employee_id']]);
            } else {
                respond(['success' => false, 'message' => 'Failed to create account'], 500);
            }
            
        } else {
            // For new faculty - full registration
            if (empty($data['employee_id']) || empty($data['dept_id'])) {
                respond(['success' => false, 'message' => 'Missing required faculty fields'], 400);
            }
            
            $employeeId = trim($data['employee_id']);
            $deptId = intval($data['dept_id']);
            $position = isset($data['position']) ? trim($data['position']) : 'Instructor';
            $specialization = isset($data['specialization']) ? trim($data['specialization']) : null;
            
            // Check if employee ID already exists
            $stmt = $pdo->prepare('SELECT employee_id FROM tblinstructor WHERE employee_id = ?');
            $stmt->execute([$employeeId]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Employee ID already exists'], 409);
            }
            
            // Check if email exists anywhere
            $stmt = $pdo->prepare('SELECT email FROM tblinstructor WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            $stmt = $pdo->prepare('SELECT email FROM tblstudent WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                respond(['success' => false, 'message' => 'Email address already registered'], 409);
            }
            
            // Insert new faculty
            $stmt = $pdo->prepare('
                INSERT INTO tblinstructor (employee_id, first_name, last_name, email, dept_id, position, specialization, password, is_deleted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
            ');
            $result = $stmt->execute([$employeeId, $firstName, $lastName, $email, $deptId, $position, $specialization, $password]);
            
            if ($result) {
                respond(['success' => true, 'message' => 'Faculty account created successfully', 'user_id' => $employeeId]);
            } else {
                respond(['success' => false, 'message' => 'Failed to create faculty account'], 500);
            }
        }
    } else {
        respond(['success' => false, 'message' => 'Only student and faculty registration is supported'], 400);
    }
    
} catch (PDOException $e) {
    error_log('Registration PDO error: ' . $e->getMessage());
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log('Registration error: ' . $e->getMessage());
    respond(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()], 500);
}
?>