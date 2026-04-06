<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include 'db.php';

require_admin_session('../frontend/admin_login.php', 'Admin access is required.');

if (!db_is_available()) {
    set_flash_message('error', db_error_message());
    redirect_to('../frontend/add_position.php');
}

require_valid_csrf('../frontend/add_position.php');

$position_name = trim($_POST['position_name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($position_name === '') {
    set_flash_message('error', 'Position name is required.');
    redirect_to('../frontend/add_position.php');
}

try {
    $conn->selectCollection('positions')->insertOne([
        'position_name' => $position_name,
        'description' => $description,
        'created_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
    ]);
    set_flash_message('success', 'Position added successfully.');
} catch (\Exception $e) {
    set_flash_message('error', 'Unable to add position.');
}

redirect_to('../frontend/add_position.php');
?>
