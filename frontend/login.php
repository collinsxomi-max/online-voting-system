<?php include '../includes/header.php'; ?>

<div class="page-center">
  <div class="card">
    <h2>Student Login</h2>
    <p>Sign in with your registration number to access the voting portal.</p>

    <form action="<?= $baseUrl ?>/backend/login.php" method="post">
      <div class="form-group">
        <label for="reg_no">Registration Number</label>
        <input id="reg_no" class="form-control" type="text" name="reg_no" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" type="password" name="password" required>
      </div>

      <button class="button button-primary" type="submit">Login</button>

      <p style="text-align:center; margin-top: 16px;">
        <a href="register.php">Create an account</a>
      </p>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
