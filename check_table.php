<?php
require_once 'api/db.php';

try {
    $stmt = $pdo->query('DESCRIBE tblstudent');
    echo "tblstudent columns:\n";
    while ($row = $stmt->fetch()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>