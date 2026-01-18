<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(['success' => true]);
    exit;
}

require_once __DIR__ . '/db.php';

function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 1000; // Default to high number for dropdown
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $sql = '
            SELECT 
                s.student_id,
                s.student_no,
                s.first_name,
                s.last_name,
                s.middle_name,
                s.email,
                s.year_level,
                s.program_id,
                s.password,
                p.program_code,
                p.program_name
            FROM tblstudent s
            LEFT JOIN tblprogram p ON s.program_id = p.program_id
            WHERE s.is_deleted = 0
        ';
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= ' AND (s.student_no LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)';
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }
        
        $sql .= ' ORDER BY s.year_level, s.student_no LIMIT ?';
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total count
        $countSql = 'SELECT COUNT(*) as total FROM tblstudent s WHERE s.is_deleted = 0';
        $countParams = [];
        
        if (!empty($search)) {
            $countSql .= ' AND (s.student_no LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)';
            $countParams = [$searchParam, $searchParam, $searchParam];
        }
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        respond([
            'success' => true,
            'data' => $students,
            'total' => intval($total),
            'showing' => count($students)
        ]);
    }
    
    respond(['success' => false, 'message' => 'Method not allowed'], 405);
    
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>