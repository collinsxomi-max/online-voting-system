<?php
define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';
include '../includes/header.php';
$registrationAvailable = db_is_available();
$registrationUnavailableMessage = 'Registration is temporarily unavailable. ' . db_error_message();
?>

<div class="page-center">
  <div class="card">
    <h2>Create Account</h2>
    <p>Register to vote in the upcoming election.</p>

    <?php if (!$registrationAvailable): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars($registrationUnavailableMessage, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/backend/register.php" method="post" onsubmit="return validateRegistration();">
      <div class="form-group">
        <label for="reg_no">Registration Number</label>
        <input id="reg_no" class="form-control" type="text" name="reg_no" required <?= $registrationAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="name">Full Name</label>
        <input id="name" class="form-control" type="text" name="name" required <?= $registrationAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" class="form-control" type="email" name="email" <?= $registrationAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" type="password" name="password" required <?= $registrationAvailable ? '' : 'disabled' ?>>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" class="form-control" type="password" name="confirm_password" required <?= $registrationAvailable ? '' : 'disabled' ?>>
      </div>

      <button class="button button-primary" type="submit" <?= $registrationAvailable ? '' : 'disabled' ?>>Register</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
