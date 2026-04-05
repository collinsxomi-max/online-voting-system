<?php
define('ALLOW_DB_FAILURE', true);
include 'db.php';
session_start();

if (!db_is_available()) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Registration is temporarily unavailable because the database connection is down.'
    ];
    header('Location: ../frontend/register.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = trim($_POST['reg_no'] ?? '');
    $full_name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Passwords do not match.'];
        header('Location: ../frontend/register.php');
        exit;
    }

    $studentsCollection = $conn->selectCollection('students');
    $existing = $studentsCollection->findOne([
        '$or' => [
            ['email' => $email],
            ['reg_no' => $reg_no]
        ]
    ]);

    if ($existing) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Account already exists.'];
        header('Location: ../frontend/register.php');
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $studentsCollection->insertOne([
            'reg_no' => $reg_no,
            'full_name' => $full_name,
            'email' => $email,
            'password_hash' => $password_hash,
            'created_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Registration successful. Please log in.'];
        header('Location: ../frontend/login.php');
        exit;
    } catch (\Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Registration failed. Please try again.'];
        header('Location: ../frontend/register.php');
        exit;
    }
}
?>
