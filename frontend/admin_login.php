<?php
define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';
include '../includes/header.php';
$adminLoginAvailable = db_is_available();
?>

<div class="page-center">
  <div class="card">
    <h2>Admin Login</h2>
    <p>Sign in with your administrator credentials to manage the election.</p>

    <?php if (!$adminLoginAvailable): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span>Admin login is temporarily unavailable because the database connection is down.</span>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/backend/admin_login.php" method="post">
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
