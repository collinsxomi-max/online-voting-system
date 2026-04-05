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
    header('Location: ../frontend/add_candidate.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position_id = $_POST['position_id'] ?? '';

    if ($name === '' || !preg_match('/^[a-f\d]{24}$/i', $position_id)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Provide a candidate name and a valid position.'];
        header('Location: ../frontend/add_candidate.php');
        exit;
    }

    try {
        $conn->selectCollection('candidates')->insertOne([
            'name' => $name,
            'position_id' => new \MongoDB\BSON\ObjectId($position_id)
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Candidate added successfully.'];
    } catch (\Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Unable to add candidate. ' . $e->getMessage()];
    }

    header('Location: ../frontend/add_candidate.php');
    exit;
}
?>
