<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $adminUser = getenv('ADMIN_USER') ?: 'admin';
    $adminPass = getenv('ADMIN_PASS') ?: 'change-this-admin-password';

    if ($username === $adminUser && hash_equals($adminPass, $password)) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $adminUser;

        if (function_exists('log_action')) {
            log_action($conn, 'Admin logged in', 0);
        }

        header("Location: ../frontend/admin_dashboard.php");
        exit;
    }

    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Invalid admin username or password.'
    ];

    header("Location: ../frontend/admin_login.php");
    exit;
}
?>
