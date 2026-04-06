<?php
require_once __DIR__ . '/security.php';

$baseUrl = getenv('APP_BASE_PATH');
$baseUrl = is_string($baseUrl) ? trim($baseUrl) : '';

if ($baseUrl === '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $baseUrl = preg_replace('#/(frontend|backend)(/.*)?$#', '', $scriptName);
    $baseUrl = preg_replace('#/[^/]+\.php$#', '', $baseUrl);
} else {
    if ($baseUrl !== '/' && $baseUrl[0] !== '/') {
        $baseUrl = '/' . $baseUrl;
    }
}

if ($baseUrl === '/') {
    $baseUrl = '';
}

$baseUrl = rtrim($baseUrl, '/');

$isStudent = isset($_SESSION['student_reg_no']);
$isAdmin = isset($_SESSION['admin']);

$activePage = basename($_SERVER['SCRIPT_NAME']);
function navActive(string $page): string {
    global $activePage;
    return $activePage === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Voting System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= $baseUrl ?>/assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="site-brand">
            <div>
                <h1>Secure Voting System</h1>
                <p class="site-tagline">University Election Portal</p>
            </div>
        </div>

        <nav class="site-nav">
            <a href="<?= $baseUrl ?>/index.php" class="<?= navActive('index.php') ?>">Home</a>
            <a href="<?= $baseUrl ?>/frontend/vote.php" class="<?= navActive('vote.php') ?>">Elections</a>
            <a href="#how" class="<?= $activePage === 'index.php' ? 'active' : '' ?>">How It Works</a>
            <a href="<?= $baseUrl ?>/frontend/results.php" class="<?= navActive('results.php') ?>">Results</a>

            <?php if ($isStudent): ?>
                <a href="<?= $baseUrl ?>/frontend/dashboard.php" class="<?= navActive('dashboard.php') ?>">Dashboard</a>
            <?php endif; ?>

            <?php if ($isStudent || $isAdmin): ?>
                <form action="<?= $baseUrl ?>/backend/logout.php" method="post" style="display:inline;">
                    <?= csrf_input() ?>
                    <button type="submit" class="button button-secondary">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?= $baseUrl ?>/frontend/login.php" class="button button-secondary">Login</a>
                <a href="<?= $baseUrl ?>/frontend/register.php" class="button button-danger">Register</a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <a href="<?= $baseUrl ?>/frontend/admin_dashboard.php" class="<?= navActive('admin_dashboard.php') ?>">Admin</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>/frontend/admin_login.php" class="<?= navActive('admin_login.php') ?>">Admin</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="main">
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert <?= htmlspecialchars($_SESSION['flash']['type']) ?>">
                <span class="icon"><?= $_SESSION['flash']['type'] === 'success' ? '&#10003;' : '&#9888;' ?></span>
                <span><?= htmlspecialchars($_SESSION['flash']['message']) ?></span>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
