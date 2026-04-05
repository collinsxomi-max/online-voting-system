<?php
session_start();
if (!isset($_SESSION['student_reg_no'])) {
    header('Location: login.php');
    exit;
}

define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';

$studentRegNo = $_SESSION['student_reg_no'];
$fullName = 'Student';
$dbAvailable = db_is_available();
$dbError = $dbAvailable ? null : db_error_message();

if ($dbAvailable) {
    $student = $conn->selectCollection('students')->findOne(['reg_no' => $studentRegNo]);
    if ($student) {
        $fullName = $student['full_name'];
    }

    $totalVoters = $conn->selectCollection('students')->countDocuments();
    $votesCast = $conn->selectCollection('votes')->countDocuments();
} else {
    $totalVoters = 0;
    $votesCast = 0;
}

include '../includes/header.php';
?>

<div class="dashboard">
  <?php include '../includes/sidebar_student.php'; ?>

  <section class="panel">
    <?php if (!$dbAvailable): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars($dbError) ?></span>
      </div>
    <?php endif; ?>

    <div class="profile-card">
      <div class="profile-badge">
        <div class="profile-avatar"><?= strtoupper(substr($fullName, 0, 1)) ?></div>
        <div>
          <p class="profile-title">Welcome, <?= htmlspecialchars($fullName) ?></p>
          <p class="profile-subtitle">Election Status: <span class="status-pill status-active">ACTIVE</span></p>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <p>Total Voters</p>
          <div class="stat-value"><?= (int)$totalVoters ?></div>
        </div>
        <div class="stat-card">
          <p>Votes Cast</p>
          <div class="stat-value"><?= (int)$votesCast ?></div>
        </div>
      </div>

      <div style="margin-top: 22px; display: grid; gap: 12px;">
        <a href="vote.php" class="button button-primary">Go Vote</a>
        <a href="results.php" class="button button-secondary">View Results</a>
        <a href="view_audit.php" class="button button-secondary">Audit Logs</a>
      </div>
    </div>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
