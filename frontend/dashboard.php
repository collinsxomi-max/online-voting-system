<?php
session_start();
if (!isset($_SESSION['student_reg_no'])) {
    header('Location: login.php');
    exit;
}

include '../backend/db.php';

$studentRegNo = $_SESSION['student_reg_no'];
$fullName = 'Student';

$student = $conn->selectCollection('students')->findOne(['reg_no' => $studentRegNo]);
if ($student) {
    $fullName = $student['full_name'];
}

$totalVoters = $conn->selectCollection('students')->countDocuments();
$votesCast = $conn->selectCollection('votes')->countDocuments();

include '../includes/header.php';
?>

<div class="dashboard">
  <?php include '../includes/sidebar_student.php'; ?>

  <section class="panel">
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
