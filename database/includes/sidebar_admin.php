<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>

<aside class="sidebar">
  <h3>Admin Panel</h3>
  <a href="admin_dashboard.php" class="<?= $current === 'admin_dashboard.php' ? 'active' : '' ?>">Dashboard</a>
  <a href="add_position.php" class="<?= $current === 'add_position.php' ? 'active' : '' ?>">Positions</a>
  <a href="add_candidate.php" class="<?= $current === 'add_candidate.php' ? 'active' : '' ?>">Candidates</a>
  <a href="results.php" class="<?= $current === 'results.php' ? 'active' : '' ?>">Results</a>
  <a href="view_audit.php" class="<?= $current === 'view_audit.php' ? 'active' : '' ?>">Audit</a>
</aside>
