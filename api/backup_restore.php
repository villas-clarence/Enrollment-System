<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    echo json_encode(['success'=>true]); 
    exit; 
}

require_once __DIR__ . '/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : '';

// Validate table name
$allowedTables = ['tblsection', 'tblenrollment', 'tblstudent', 'tblcourse', 'tblinstructor', 'tbldepartment', 'tblprogram', 'tblcourse_prerequisite', 'tblterm', 'tblroom'];
if (!in_array($table, $allowedTables)) {
    echo json_encode(['success'=>false, 'message'=>'Invalid table name']);
    exit;
}

try {
    if ($action === 'backup') {
        // Create backup
        $stmt = $pdo->query("SELECT * FROM $table");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create backup filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "{$table}_backup_{$timestamp}.json";
        
        // Return data as JSON for download
        header('Content-Type: application/json');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        echo json_encode([
            'table' => $table,
            'timestamp' => $timestamp,
            'count' => count($data),
            'data' => $data
        ], JSON_PRETTY_PRINT);
        exit;
        
    } elseif ($action === 'restore') {
        // Restore from uploaded JSON file
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['data']) || !is_array($input['data'])) {
            echo json_encode(['success'=>false, 'message'=>'Invalid backup data']);
            exit;
        }
        
        $backupData = $input['data'];
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Clear existing data
            $pdo->exec("DELETE FROM $table");
            
            // Restore data
            $restored = 0;
            foreach ($backupData as $row) {
                $columns = array_keys($row);
                $placeholders = array_fill(0, count($columns), '?');
                
                $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($row));
                $restored++;
            }
            
            $pdo->commit();
            echo json_encode([
                'success' => true, 
                'message' => "Successfully restored {$restored} records to {$table}",
                'count' => $restored
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        echo json_encode(['success'=>false, 'message'=>'Invalid action']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success'=>false, 'message'=>'Database error: ' . $e->getMessage()]);
}
?>
