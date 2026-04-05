<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>

<aside class="sidebar">
  <h3>Student Panel</h3>
  <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
  <a href="vote.php" class="<?= $current === 'vote.php' ? 'active' : '' ?>">Vote</a>
  <a href="results.php" class="<?= $current === 'results.php' ? 'active' : '' ?>">Results</a>
  <a href="view_audit.php" class="<?= $current === 'view_audit.php' ? 'active' : '' ?>">Audit</a>
</aside>
