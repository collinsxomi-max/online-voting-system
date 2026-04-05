<?php
session_start();
if (!isset($_SESSION['student_reg_no']) && !isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

define('ALLOW_DB_FAILURE', true);
include '../backend/db.php';

$logs = [];
if (db_is_available()) {
    foreach ($conn->selectCollection('audit_log')->find([], ['sort' => ['_id' => -1]]) as $log) {
        $logs[] = [
            'log_id' => (string)$log['_id'],
            'timestamp' => isset($log['timestamp']) ? $log['timestamp']->toDateTime()->format('Y-m-d H:i:s') : 'N/A',
            'action' => $log['action'] ?? '',
            'user_id' => $log['user_id'] ?? 'system'
        ];
    }
}

include '../includes/header.php';
?>

<div class="dashboard">
  <?php if (isset($_SESSION['admin'])): ?>
    <?php include '../includes/sidebar_admin.php'; ?>
  <?php else: ?>
    <?php include '../includes/sidebar_student.php'; ?>
  <?php endif; ?>

  <section class="panel">
    <h2>Audit Log</h2>

    <?php if (!db_is_available()): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars(db_error_message()) ?></span>
      </div>
    <?php endif; ?>

    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Time</th>
          <th>Action</th>
          <th>User</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= htmlspecialchars($log['log_id']) ?></td>
            <td><?= htmlspecialchars($log['timestamp']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars((string) $log['user_id']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
