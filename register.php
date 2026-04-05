<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-center">
  <div class="card">
    <h2>Create Account</h2>
    <p>Register to vote in the upcoming election.</p>

    <form action="<?= $baseUrl ?>/backend/register.php" method="post" onsubmit="return validateRegistration();">
      <div class="form-group">
        <label for="reg_no">Registration Number</label>
        <input id="reg_no" class="form-control" type="text" name="reg_no" required>
      </div>

      <div class="form-group">
        <label for="name">Full Name</label>
        <input id="name" class="form-control" type="text" name="name" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" class="form-control" type="email" name="email">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" type="password" name="password" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" class="form-control" type="password" name="confirm_password" required>
      </div>

      <button class="button button-primary" type="submit">Register</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
