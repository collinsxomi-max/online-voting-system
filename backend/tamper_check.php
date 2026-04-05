<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

$issues = [];
$votes = db_find_many('votes');

foreach ($votes as $vote) {
    $integrity = db_find_one('integrity', ['vote_id' => (int)$vote['vote_id']]);
    if ($integrity) {
        $recalc = hash('sha256', $vote['student_id'] . "-" . $vote['position_id'] . "-" . $vote['candidate_id'] . "-" . $vote['vote_id']);
        if (!hash_equals($recalc, $integrity['vote_hash'])) {
            $issues[] = $vote['vote_id'];
        }
    }
}

if (function_exists('log_action')) {
    log_action($conn, 'Tamper check performed', 0);
}

include '../includes/header.php';
?>

<div class="page-center">
  <div class="card">
    <h2>Tamper Detection</h2>
    <p>The system checks vote hashes against expected values.</p>

    <?php if (empty($issues)): ?>
      <div class="alert success">
        <span class="icon">✅</span>
        <span>No tampering detected.</span>
      </div>
    <?php else: ?>
      <div class="alert error">
        <span class="icon">⚠️</span>
        <span>Tampering detected for vote IDs: <?= htmlspecialchars(implode(', ', $issues)) ?></span>
      </div>
    <?php endif; ?>

    <div style="margin-top: 16px;">
      <a href="../frontend/admin_dashboard.php" class="button button-secondary">Return to Dashboard</a>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>