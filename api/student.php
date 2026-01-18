<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Max-Age: 3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/db.php';

// Helper functions
function readJsonBody() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function respond($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function validateStudentData($data, $isUpdate = false) {
    $errors = [];
    
    // Required fields for both create and update
    $required = ['student_no', 'last_name', 'first_name', 'email', 'gender', 'birthdate'];
    foreach ($required as $field) {
        if (!$isUpdate) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = 'This field is required';
            }
        } else if (array_key_exists($field, $data) && trim($data[$field]) === '') {
            $errors[$field] = 'This field cannot be empty';
        }
    }
    
    // Validate email format
    if (isset($data['email']) && $data['email'] !== '') {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
    }
    
    // Validate student number format (e.g., 2023-00429-TG-0)
    if (isset($data['student_no']) && $data['student_no'] !== '') {
        if (!preg_match('/^\d{4}-\d{5}-[A-Z]{2}-\d$/', $data['student_no'])) {
            $errors['student_no'] = 'Student number must be in the format: YYYY-XXXXX-TG-0';
        }
    }
    
    // Validate date format
    if (isset($data['birthdate']) && $data['birthdate'] !== '') {
        $date = DateTime::createFromFormat('Y-m-d', $data['birthdate']);
        if (!$date || $date->format('Y-m-d') !== $data['birthdate']) {
            $errors['birthdate'] = 'Invalid date format. Use YYYY-MM-DD';
        } else {
            // Check if birthdate is not in the future
            $today = new DateTime();
            if ($date > $today) {
                $errors['birthdate'] = 'Birthdate cannot be in the future';
            }
        }
    }
    
    // Validate gender
    if (isset($data['gender']) && $data['gender'] !== '') {
        $validGenders = ['Male', 'Female', 'Other'];
        if (!in_array($data['gender'], $validGenders)) {
            $errors['gender'] = 'Invalid gender value';
        }
    }
    
    // Validate year level
    if (isset($data['year_level']) && $data['year_level'] !== '') {
        if (!is_numeric($data['year_level']) || $data['year_level'] < 1 || $data['year_level'] > 10) {
            $errors['year_level'] = 'Year level must be between 1 and 10';
        }
    }
    
    return $errors;
}

// Get request method and ID
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    // GET: Retrieve student(s)
    if ($method === 'GET') {
        if ($id) {
            // Get single student by ID
            $stmt = $pdo->prepare('SELECT s.*, p.program_code, p.program_name 
                                 FROM tblstudent s 
                                 LEFT JOIN tblprogram p ON s.program_id = p.program_id 
                                 WHERE s.student_id = ?');
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                respond(['success' => false, 'message' => 'Student not found'], 404);
            }
            
            respond(['success' => true, 'data' => $student]);
        } else {
            // Get all students with pagination
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? min(max(1, intval($_GET['limit'])), 1000) : 50;
            $offset = ($page - 1) * $limit;
            
            // Count all records (including soft-deleted)
            $countStmt = $pdo->query('SELECT COUNT(*) FROM tblstudent WHERE 1=1');
            $total = $countStmt->fetchColumn();
            
            // Get sort parameters
            $orderBy = isset($_GET['order_by']) && in_array($_GET['order_by'], ['student_id', 'last_name', 'first_name', 'student_no']) 
                      ? $_GET['order_by'] 
                      : 'student_id';
            $orderDir = isset($_GET['order_dir']) && strtoupper($_GET['order_dir']) === 'ASC' ? 'ASC' : 'DESC';
            
            // Get all students including soft-deleted ones for now
            $sql = 'SELECT s.*, p.program_code, p.program_name 
                   FROM tblstudent s 
                   LEFT JOIN tblprogram p ON s.program_id = p.program_id 
                   WHERE 1=1 
                   ORDER BY ' . $orderBy . ' ' . $orderDir . ' 
                   LIMIT ? OFFSET ?';
            
            // Debug: Output the SQL query
            error_log("SQL Query: $sql");
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$limit, $offset]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log the number of students found
            error_log("Total students found: " . count($students));
            
            respond([
                'success' => true,
                'data' => $students,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => ceil($total / $limit)
                ]
            ]);
        }
    }

    // POST: Create new student
    if ($method === 'POST') {
        $body = readJsonBody();
        
        // Validate input data
        $validationErrors = validateStudentData($body);
        if (!empty($validationErrors)) {
            respond([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors
            ], 422);
        }
        
        // Prepare data
        $studentNo = strtoupper(trim($body['student_no']));
        $lastName = trim($body['last_name']);
        $firstName = trim($body['first_name']);
        $middleName = isset($body['middle_name']) ? trim($body['middle_name']) : null;
        $email = strtolower(trim($body['email']));
        $gender = trim($body['gender']);
        $birthdate = trim($body['birthdate']);
        $yearLevel = isset($body['year_level']) ? intval($body['year_level']) : null;
        $programId = isset($body['program_id']) ? intval($body['program_id']) : null;
        
        // Check for duplicate student number or email
        $duplicateCheck = $pdo->prepare('SELECT student_id FROM tblstudent WHERE student_no = ? OR email = ? LIMIT 1');
        $duplicateCheck->execute([$studentNo, $email]);
        $duplicate = $duplicateCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($duplicate) {
            respond([
                'success' => false,
                'message' => 'A student with this student number or email already exists',
                'duplicate_id' => $duplicate['student_id']
            ], 409);
        }
        
        // Check if program exists if provided
        if ($programId && $programId > 0) {
            $programCheck = $pdo->prepare('SELECT program_id FROM tblprogram WHERE program_id = ?');
            $programCheck->execute([$programId]);
            if (!$programCheck->fetch()) {
                respond(['success' => false, 'message' => 'Invalid program ID'], 400);
            }
        } else {
            $programId = null; // Ensure it's null if not provided or 0
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Insert new student
            $stmt = $pdo->prepare('INSERT INTO tblstudent 
                (student_no, last_name, first_name, middle_name, email, gender, birthdate, year_level, program_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
                
            $stmt->execute([
                $studentNo,
                $lastName,
                $firstName,
                $middleName,
                $email,
                $gender,
                $birthdate,
                $yearLevel,
                $programId
            ]);
            
            $newId = (int)$pdo->lastInsertId();
            
            // Get the newly created student
            $stmt = $pdo->prepare('SELECT s.*, p.program_code, p.program_name 
                                 FROM tblstudent s 
                                 LEFT JOIN tblprogram p ON s.program_id = p.program_id 
                                 WHERE s.student_id = ?');
            $stmt->execute([$newId]);
            $newStudent = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pdo->commit();
            
            // Return success response
            respond([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => $newStudent
            ], 201);
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            
            // Handle specific database errors
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                respond([
                    'success' => false,
                    'message' => 'A student with this student number or email already exists'
                ], 409);
            } else {
                error_log('Database error in student creation: ' . $e->getMessage());
                respond([
                    'success' => false,
                    'message' => 'Failed to create student',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    // PUT: Update existing student
    if ($method === 'PUT') {
        if (!$id) {
            respond(['success' => false, 'message' => 'Student ID is required'], 400);
        }
        
        $body = readJsonBody();
        
        // Validate input data
        $validationErrors = validateStudentData($body, true);
        if (!empty($validationErrors)) {
            respond([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors
            ], 422);
        }
        
        // Check if student exists and is not deleted
        $stmt = $pdo->prepare('SELECT * FROM tblstudent WHERE student_id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing) {
            respond(['success' => false, 'message' => 'Student not found or has been deleted'], 404);
        }
        
        // Prepare update data
        $updateFields = [];
        $params = [];
        
        // List of fields that can be updated
        $updatableFields = [
            'student_no' => 'student_no',
            'last_name' => 'last_name',
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'email' => 'email',
            'gender' => 'gender',
            'birthdate' => 'birthdate',
            'year_level' => 'year_level',
            'program_id' => 'program_id'
        ];
        
        // Check for duplicate student number or email if they're being updated
        if ((isset($body['student_no']) && $body['student_no'] !== $existing['student_no']) || 
            (isset($body['email']) && strtolower($body['email']) !== strtolower($existing['email']))) {
            
            $studentNo = isset($body['student_no']) ? strtoupper(trim($body['student_no'])) : $existing['student_no'];
            $email = isset($body['email']) ? strtolower(trim($body['email'])) : $existing['email'];
            
            $duplicateCheck = $pdo->prepare('SELECT student_id FROM tblstudent WHERE student_id != ? AND (student_no = ? OR email = ?) LIMIT 1');
            $duplicateCheck->execute([$id, $studentNo, $email]);
            $duplicate = $duplicateCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($duplicate) {
                respond([
                    'success' => false,
                    'message' => 'Another student with this student number or email already exists',
                    'duplicate_id' => $duplicate['student_id']
                ], 409);
            }
        }
        
        // Check if program exists if provided
        if (isset($body['program_id']) && $body['program_id'] !== $existing['program_id']) {
            $programCheck = $pdo->prepare('SELECT program_id FROM tblprogram WHERE program_id = ?');
            $programCheck->execute([$body['program_id']]);
            if (!$programCheck->fetch()) {
                respond(['success' => false, 'message' => 'Invalid program ID'], 400);
            }
        }
        
        // Build dynamic update query
        foreach ($updatableFields as $field => $dbField) {
            if (array_key_exists($field, $body)) {
                $value = $body[$field];
                
                // Skip if value hasn't changed
                if ($field === 'email' && isset($existing[$dbField])) {
                    if (strtolower($value) === strtolower($existing[$dbField])) continue;
                } elseif (isset($existing[$dbField]) && $value == $existing[$dbField]) {
                    continue;
                }
                
                // Format values
                if ($field === 'student_no') {
                    $value = strtoupper(trim($value));
                } elseif ($field === 'email') {
                    $value = strtolower(trim($value));
                } elseif ($field === 'year_level' || $field === 'program_id') {
                    $value = !empty($value) ? (int)$value : null;
                } elseif ($field === 'middle_name') {
                    $value = !empty($value) ? trim($value) : null;
                } else {
                    $value = trim($value);
                }
                
                $updateFields[] = "$dbField = ?";
                $params[] = $value;
            }
        }
        
        // If no fields to update, return success
        if (empty($updateFields)) {
            respond([
                'success' => true,
                'message' => 'No changes detected',
                'data' => $existing
            ]);
        }
        
        // Add updated_at and student_id to params
        $updateFields[] = 'updated_at = NOW()';
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Build and execute the update query
            $sql = 'UPDATE tblstudent SET ' . implode(', ', $updateFields) . ' WHERE student_id = ?';
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // Get the updated student with program info
            $stmt = $pdo->prepare('SELECT s.*, p.program_code, p.program_name 
                                 FROM tblstudent s 
                                 LEFT JOIN tblprogram p ON s.program_id = p.program_id 
                                 WHERE s.student_id = ?');
            $stmt->execute([$id]);
            $updatedStudent = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pdo->commit();
            
            respond([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => $updatedStudent
            ]);
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            
            // Handle specific database errors
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                respond([
                    'success' => false,
                    'message' => 'A student with this student number or email already exists'
                ], 409);
            } else {
                error_log('Database error in student update: ' . $e->getMessage());
                respond([
                    'success' => false,
                    'message' => 'Failed to update student',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    // DELETE: Soft delete a student
    if ($method === 'DELETE') {
        if (!$id) {
            respond(['success' => false, 'message' => 'Student ID is required'], 400);
        }
        
        // Check if student exists and is not already deleted
        $stmt = $pdo->prepare('SELECT student_id FROM tblstudent WHERE student_id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            respond(['success' => false, 'message' => 'Student not found or already deleted'], 404);
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Soft delete the student
            $stmt = $pdo->prepare('UPDATE tblstudent SET is_deleted = 1, deleted_at = NOW() WHERE student_id = ?');
            $stmt->execute([$id]);
            
            // Also delete any enrollments for this student
            $stmt = $pdo->prepare('DELETE FROM tblenrollment WHERE student_id = ?');
            $stmt->execute([$id]);
            
            $pdo->commit();
            
            respond([
                'success' => true,
                'message' => 'Student deleted successfully',
                'deleted_id' => $id
            ]);
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            
            error_log('Database error in student deletion: ' . $e->getMessage());
            respond([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method not allowed
    respond([
        'success' => false,
        'message' => 'Method not allowed',
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
    ], 405);
    
} catch (PDOException $e) {
    // Log the full error for debugging
    error_log('Database error: ' . $e->getMessage());
    
    // Return a generic error message to the client
    respond([
        'success' => false,
        'message' => 'A database error occurred',
        'error_code' => $e->getCode()
    ], 500);
    
} catch (Exception $e) {
    // Catch any other exceptions
    error_log('Unexpected error: ' . $e->getMessage());
    
    respond([
        'success' => false,
        'message' => 'An unexpected error occurred',
        'error' => $e->getMessage()
    ], 500);
}
?>









