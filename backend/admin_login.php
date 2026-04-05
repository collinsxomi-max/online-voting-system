<?php
session_start();
define('ALLOW_DB_FAILURE', true);
include 'db.php';

if (!db_is_available()) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Admin login is temporarily unavailable because the database connection is down.'
    ];
    header("Location: ../frontend/admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $adminUser = readEnvironmentValue('ADMIN_USER') ?: 'admin';
    $adminPass = readEnvironmentValue('ADMIN_PASS') ?: 'change-this-admin-password';

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
