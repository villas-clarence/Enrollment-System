<?php
// api/section_enrollment.php
header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Check if requesting distinct blocks
        if (isset($_GET['blocks']) && $_GET['blocks'] == '1') {
            // Get distinct section codes (blocks)
            $sql = "SELECT DISTINCT section_code FROM tblsection ORDER BY section_code";
            try {
                $stmt = $pdo->query($sql);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Normalize in PHP - add space between letters and numbers
                $uniqueCodes = [];
                $seen = [];
                foreach ($data as $row) {
                    // Add space between letters and numbers (e.g., DIT3-1 -> DIT 3-1)
                    $code = preg_replace('/([A-Za-z])(\d)/', '$1 $2', trim($row['section_code']));
                    if (!isset($seen[$code])) {
                        $seen[$code] = true;
                        $uniqueCodes[] = ['section_code' => $code];
                    }
                }
                
                // Sort
                usort($uniqueCodes, function($a, $b) {
                    return strcmp($a['section_code'], $b['section_code']);
                });
                
                echo json_encode(['success'=>true, 'data'=>$uniqueCodes]);
            } catch (PDOException $e) {
                echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
            }
            break;
        }
        
        // List all enrollments with complete section details
        $sql = "SELECT e.enrollment_id, e.student_id, e.section_id, e.date_enrolled, e.status, e.letter_grade,
                       sec.section_code, sec.course_id, sec.term_id, sec.instructor_id, 
                       sec.day_pattern, sec.start_time, sec.end_time, sec.room_id, sec.max_capacity,
                       s.student_no, s.last_name, s.first_name,
                       c.course_title as course_name, c.course_code,
                       i.first_name as instructor_first_name, i.last_name as instructor_last_name,
                       r.room_code as room_name, r.building,
                       t.term_code as term_name
                FROM tblenrollment e
                JOIN tblsection sec ON e.section_id = sec.section_id
                JOIN tblstudent s ON e.student_id = s.student_id
                LEFT JOIN tblcourse c ON sec.course_id = c.course_id
                LEFT JOIN tblinstructor i ON sec.instructor_id = i.instructor_id
                LEFT JOIN tblroom r ON sec.room_id = r.room_id
                LEFT JOIN tblterm t ON sec.term_id = t.term_id
                WHERE e.is_deleted = 0
                ORDER BY sec.section_code, s.last_name, s.first_name";
        
        try {
            $stmt = $pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success'=>true, 'data'=>$data]);
        } catch (PDOException $e) {
            echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
        }
        break;
        
    case 'POST':
        // Add new enrollment
        $input = json_decode(file_get_contents('php://input'), true);
        $student_id = isset($input['student_id']) ? intval($input['student_id']) : 0;
        $date_enrolled = isset($input['enrollment_date']) ? $input['enrollment_date'] : date('Y-m-d');
        $status = isset($input['status']) ? $input['status'] : 'Active';
        
        // Check if this is individual section enrollment or block enrollment
        $section_id = isset($input['section_id']) ? intval($input['section_id']) : 0;
        $section_code = isset($input['section_code']) ? trim($input['section_code']) : '';
        
        if ($student_id <= 0) {
            echo json_encode(['success'=>false, 'message'=>'Invalid student ID']);
            break;
        }
        
        try {
            // INDIVIDUAL SECTION ENROLLMENT (when section_id is provided)
            if ($section_id > 0) {
                // Check if student is already enrolled in this section
                $checkSql = "SELECT enrollment_id FROM tblenrollment WHERE student_id = ? AND section_id = ? AND is_deleted = 0";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([$student_id, $section_id]);
                
                if ($checkStmt->fetch()) {
                    echo json_encode(['success'=>false, 'message'=>'Student is already enrolled in this section']);
                    break;
                }
                
                // Enroll student to this specific section
                $sql = "INSERT INTO tblenrollment (student_id, section_id, date_enrolled, status) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$student_id, $section_id, $date_enrolled, $status]);
                
                echo json_encode(['success'=>true, 'message'=>'Student enrolled successfully', 'enrollment_id'=>$pdo->lastInsertId()]);
                break;
            }
            
            // BLOCK ENROLLMENT (when section_code is provided)
            if (!empty($section_code)) {
                // Normalize section code - remove extra spaces for matching
                $normalizedCode = preg_replace('/\s+/', '', $section_code); // Remove all spaces
                
                // Get all sections with this section_code (match both with and without spaces)
                $sectionsSql = "SELECT section_id FROM tblsection WHERE REPLACE(section_code, ' ', '') = ?";
                $sectionsStmt = $pdo->prepare($sectionsSql);
                $sectionsStmt->execute([$normalizedCode]);
                $allSections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // If no sections found, create a default section for this block
                if (empty($allSections)) {
                    // Create a new section with this section_code
                    $createSql = "INSERT INTO tblsection (section_code, max_capacity) VALUES (?, 40)";
                    $createStmt = $pdo->prepare($createSql);
                    $createStmt->execute([$section_code]);
                    $newSectionId = $pdo->lastInsertId();
                    $allSections = [['section_id' => $newSectionId]];
                }
                
                if (empty($allSections)) {
                    echo json_encode(['success'=>false, 'message'=>'No sections found for this block']);
                    break;
                }
                
                $enrolledCount = 0;
                $skippedCount = 0;
                
                foreach ($allSections as $section) {
                    $sec_id = $section['section_id'];
                    
                    // Check if student is already enrolled in this section
                    $checkSql = "SELECT enrollment_id FROM tblenrollment WHERE student_id = ? AND section_id = ? AND is_deleted = 0";
                    $checkStmt = $pdo->prepare($checkSql);
                    $checkStmt->execute([$student_id, $sec_id]);
                    
                    if ($checkStmt->fetch()) {
                        $skippedCount++;
                        continue; // Skip if already enrolled
                    }
                    
                    // Enroll student to this section
                    $sql = "INSERT INTO tblenrollment (student_id, section_id, date_enrolled, status) VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$student_id, $sec_id, $date_enrolled, $status]);
                    $enrolledCount++;
                }
                
                if ($enrolledCount > 0) {
                    echo json_encode(['success'=>true, 'message'=>"Student enrolled to {$enrolledCount} course(s) in block {$section_code}. {$skippedCount} already enrolled."]);
                } else {
                    echo json_encode(['success'=>false, 'message'=>'Student is already enrolled in all courses for this block']);
                }
                break;
            }
            
            echo json_encode(['success'=>false, 'message'=>'Please provide either section_id (individual) or section_code (block enrollment)']);
            
        } catch (PDOException $e) {
            echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        // Update enrollment
        parse_str($_SERVER['QUERY_STRING'], $params);
        $enrollment_id = isset($params['id']) ? intval($params['id']) : 0;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($enrollment_id <= 0) {
            echo json_encode(['success'=>false, 'message'=>'Invalid enrollment ID']);
            break;
        }
        
        $section_id = isset($input['section_id']) ? intval($input['section_id']) : 0;
        $student_id = isset($input['student_id']) ? intval($input['student_id']) : 0;
        $date_enrolled = isset($input['enrollment_date']) ? $input['enrollment_date'] : date('Y-m-d');
        $status = isset($input['status']) ? $input['status'] : 'Active';
        
        try {
            $sql = "UPDATE tblenrollment SET student_id=?, section_id=?, date_enrolled=?, status=? WHERE enrollment_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $section_id, $date_enrolled, $status, $enrollment_id]);
            echo json_encode(['success'=>true, 'message'=>'Enrollment updated successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        // Soft delete: mark as deleted
        parse_str($_SERVER['QUERY_STRING'], $params);
        $enrollment_id = isset($params['id']) ? intval($params['id']) : 0;
        
        if ($enrollment_id <= 0) {
            echo json_encode(['success'=>false, 'message'=>'Invalid enrollment ID']);
            break;
        }
        
        try {
            $sql = "UPDATE tblenrollment SET is_deleted=1, deleted_at=NOW() WHERE enrollment_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$enrollment_id]);
            echo json_encode(['success'=>true, 'message'=>'Enrollment deleted successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success'=>false, 'message'=>'Invalid request method']);
}
?>