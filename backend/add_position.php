<?php
session_start();
define('ALLOW_DB_FAILURE', true);
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

if (!db_is_available()) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => db_error_message()];
    header('Location: ../frontend/add_position.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_name = trim($_POST['position_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($position_name === '') {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Position name is required.'];
        header('Location: ../frontend/add_position.php');
        exit;
    }

    try {
        $conn->selectCollection('positions')->insertOne([
            'position_name' => $position_name,
            'description' => $description,
            'created_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Position added successfully.'];
    } catch (\Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Unable to add position.'];
    }

    header('Location: ../frontend/add_position.php');
    exit;
}
?>
