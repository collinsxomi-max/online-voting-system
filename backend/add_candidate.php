<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include 'db.php';

require_admin_session('../frontend/admin_login.php', 'Admin access is required.');

if (!db_is_available()) {
    set_flash_message('error', db_error_message());
    redirect_to('../frontend/add_candidate.php');
}

require_valid_csrf('../frontend/add_candidate.php');

$name = trim($_POST['name'] ?? '');
$position_id = $_POST['position_id'] ?? '';

if ($name === '' || !preg_match('/^[a-f\d]{24}$/i', $position_id)) {
    set_flash_message('error', 'Provide a candidate name and a valid position.');
    redirect_to('../frontend/add_candidate.php');
}

$positionObjectId = new \MongoDB\BSON\ObjectId($position_id);
$position = $conn->selectCollection('positions')->findOne(['_id' => $positionObjectId]);
if (!$position) {
    set_flash_message('error', 'The selected position no longer exists.');
    redirect_to('../frontend/add_candidate.php');
}

try {
    $conn->selectCollection('candidates')->insertOne([
        'name' => $name,
        'position_id' => $positionObjectId
    ]);
    set_flash_message('success', 'Candidate added successfully.');
} catch (\Exception $e) {
    set_flash_message('error', 'Unable to add candidate. ' . $e->getMessage());
}

redirect_to('../frontend/add_candidate.php');
?>
