<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'] ?? '';

    try {
        $conn->selectCollection('candidates')->deleteOne([
            '_id' => new \MongoDB\BSON\ObjectId($candidate_id)
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Candidate deleted successfully.'];
    } catch (\Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Unable to delete candidate.'];
    }

    header('Location: ../frontend/add_candidate.php');
    exit;
}
?>
