<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include 'db.php';

if (!db_is_available()) {
    set_flash_message('error', 'Admin login is temporarily unavailable. ' . db_error_message());
    redirect_to('../frontend/admin_login.php');
}

require_valid_csrf('../frontend/admin_login.php');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$adminUser = admin_username();
$adminPass = admin_password();

if ($adminUser === null || $adminPass === null) {
    set_flash_message('error', 'Admin credentials are not configured. Set ADMIN_USER and ADMIN_PASS, or add admin_user and admin_pass to backend/config.local.php.');
    redirect_to('../frontend/admin_login.php');
}

if (hash_equals($adminUser, $username) && hash_equals($adminPass, $password)) {
    harden_session_after_login();
    $_SESSION['admin'] = true;
    $_SESSION['admin_username'] = $adminUser;

    if (function_exists('log_action')) {
        log_action($conn, 'Admin logged in', 0);
    }

    redirect_to('../frontend/admin_dashboard.php');
}

set_flash_message('error', 'Invalid admin username or password.');
redirect_to('../frontend/admin_login.php');
?>
