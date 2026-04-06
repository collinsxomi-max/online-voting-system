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

$candidate_id = $_POST['candidate_id'] ?? '';

if (!preg_match('/^[a-f\d]{24}$/i', $candidate_id)) {
    set_flash_message('error', 'Invalid candidate selected.');
    redirect_to('../frontend/add_candidate.php');
}

try {
    $result = $conn->selectCollection('candidates')->deleteOne([
        '_id' => new \MongoDB\BSON\ObjectId($candidate_id)
    ]);

    if ($result->getDeletedCount() === 0) {
        set_flash_message('error', 'Candidate not found.');
    } else {
        set_flash_message('success', 'Candidate deleted successfully.');
    }
} catch (\Exception $e) {
    set_flash_message('error', 'Unable to delete candidate.');
}

redirect_to('../frontend/add_candidate.php');
?>
