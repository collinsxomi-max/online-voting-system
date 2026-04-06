<?php
define('ALLOW_DB_FAILURE', true);
include __DIR__ . '/../../db.php';
session_start();

if (!db_is_available()) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Login is temporarily unavailable. ' . db_error_message()
    ];
    header("Location: ../frontend/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = trim($_POST['reg_no'] ?? '');
    $password = $_POST['password'] ?? '';

    $student = $conn->selectCollection('students')->findOne(['reg_no' => $reg_no]);

    if ($student && password_verify($password, $student['password_hash'])) {
        $_SESSION['student_reg_no'] = $student['reg_no'];
        $_SESSION['student_name'] = $student['full_name'];
        log_action($conn, 'User logged in', $student['reg_no']);
        header("Location: ../frontend/dashboard.php");
        exit;
    }

    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid registration number or password.'];
    header("Location: ../frontend/login.php");
    exit;
}
?>
