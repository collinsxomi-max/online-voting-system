<?php
require_once __DIR__ . '/../includes/security.php';
require_valid_csrf('../frontend/login.php');

session_unset();
session_destroy();

redirect_to('../frontend/login.php');
?>
