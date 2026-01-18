<?php
require_once 'api/db.php';
$pdo->prepare('UPDATE tblstudent SET password = NULL WHERE student_no = ?')->execute(['2025-00102-TG-0']);
echo 'Reset Daniel password for testing';
?>