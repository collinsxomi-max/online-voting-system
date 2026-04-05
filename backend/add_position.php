<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_name = trim($_POST['position_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

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
