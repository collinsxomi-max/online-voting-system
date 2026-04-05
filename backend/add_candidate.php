<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position_id = $_POST['position_id'] ?? '';

    try {
        $conn->selectCollection('candidates')->insertOne([
            'name' => $name,
            'position_id' => new \\MongoDB\\BSON\\ObjectId($position_id)
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Candidate added successfully.'];
    } catch (\\Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Unable to add candidate. ' . $e->getMessage()];
    }

    header('Location: ../frontend/add_candidate.php');
    exit;
}
?>
