<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include 'db.php';

if (!db_is_available()) {
    set_flash_message('error', 'Registration is temporarily unavailable. ' . db_error_message());
    redirect_to('../frontend/register.php');
}

require_valid_csrf('../frontend/register.php');

$reg_no = trim($_POST['reg_no'] ?? '');
$full_name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($reg_no === '' || $full_name === '' || $password === '' || $confirm_password === '') {
    set_flash_message('error', 'Registration number, full name, and password fields are required.');
    redirect_to('../frontend/register.php');
}

if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    set_flash_message('error', 'Please provide a valid email address.');
    redirect_to('../frontend/register.php');
}

if (strlen($password) < 8) {
    set_flash_message('error', 'Password must be at least 8 characters long.');
    redirect_to('../frontend/register.php');
}

if ($password !== $confirm_password) {
    set_flash_message('error', 'Passwords do not match.');
    redirect_to('../frontend/register.php');
}

$studentsCollection = $conn->selectCollection('students');
$lookupConditions = [
    ['reg_no' => $reg_no]
];

if ($email !== '') {
    $lookupConditions[] = ['email' => $email];
}

$existing = $studentsCollection->findOne([
    '$or' => $lookupConditions
]);

if ($existing) {
    set_flash_message('error', 'Account already exists.');
    redirect_to('../frontend/register.php');
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
    set_flash_message('success', 'Registration successful. Please log in.');
    redirect_to('../frontend/login.php');
} catch (\Exception $e) {
    set_flash_message('error', 'Registration failed. Please try again.');
    redirect_to('../frontend/register.php');
}
?>
