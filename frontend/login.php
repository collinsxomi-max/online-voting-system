<?php
define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';
include '../includes/header.php';
$loginAvailable = db_is_available();
$loginUnavailableMessage = 'Login is temporarily unavailable. ' . db_error_message();
?>

<div class="page-center">
  <div class="card">
    <h2>Student Login</h2>
    <p>Sign in with your registration number to access the voting portal.</p>

    <?php if (!$loginAvailable): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars($loginUnavailableMessage, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/backend/login.php" method="post">
      <?= csrf_input() ?>
      <div class="form-group">
        <label for="reg_no">Registration Number</label>
        <input id="reg_no" class="form-control" type="text" name="reg_no" required <?= $loginAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" type="password" name="password" required <?= $loginAvailable ? '' : 'disabled' ?>>
      </div>

      <button class="button button-primary" type="submit" <?= $loginAvailable ? '' : 'disabled' ?>>Login</button>

      <p style="text-align:center; margin-top: 16px;">
        <a href="register.php">Create an account</a>
      </p>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
