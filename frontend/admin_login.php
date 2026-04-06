<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';
include '../includes/header.php';
$adminCredentialsConfigured = admin_credentials_are_configured();
$adminLoginAvailable = db_is_available() && $adminCredentialsConfigured;
$adminLoginUnavailableMessage = !$adminCredentialsConfigured
    ? 'Admin credentials are not configured. Set ADMIN_USER and ADMIN_PASS, or add admin_user and admin_pass to backend/config.local.php.'
    : 'Admin login is temporarily unavailable. ' . db_error_message();
?>

<div class="page-center">
  <div class="card">
    <h2>Admin Login</h2>
    <p>Sign in with your administrator credentials to manage the election.</p>

    <?php if (!$adminLoginAvailable): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars($adminLoginUnavailableMessage, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/backend/admin_login.php" method="post">
      <?= csrf_input() ?>
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" class="form-control" type="text" name="username" required <?= $adminLoginAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" type="password" name="password" required <?= $adminLoginAvailable ? '' : 'disabled' ?>>
      </div>

      <button class="button button-primary" type="submit" <?= $adminLoginAvailable ? '' : 'disabled' ?>>Login</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
