<?php
require_once __DIR__ . '/../../../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include __DIR__ . '/../../db.php';

if (!db_is_available()) {
    set_flash_message('error', 'Login is temporarily unavailable. ' . db_error_message());
    redirect_to('../frontend/login.php');
}

require_valid_csrf('../frontend/login.php');

$reg_no = trim($_POST['reg_no'] ?? '');
$password = $_POST['password'] ?? '';

$student = $conn->selectCollection('students')->findOne(['reg_no' => $reg_no]);

if ($student && password_verify($password, $student['password_hash'])) {
    harden_session_after_login();
    $_SESSION['student_reg_no'] = $student['reg_no'];
    $_SESSION['student_name'] = $student['full_name'];
    log_action($conn, 'User logged in', $student['reg_no']);
    redirect_to('../frontend/dashboard.php');
}

set_flash_message('error', 'Invalid registration number or password.');
redirect_to('../frontend/login.php');
?>
