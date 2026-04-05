<?php
session_start();
define('ALLOW_DB_FAILURE', true);
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

$issues = [];
if (db_is_available()) {
    $votesCollection = $conn->selectCollection('votes');
    $integrityCollection = $conn->selectCollection('integrity');

    foreach ($votesCollection->find() as $vote) {
        $voteId = (string)$vote['_id'];
        $integrityRecord = $integrityCollection->findOne(['vote_id' => $voteId]);

        if (!$integrityRecord) {
            $issues[] = $voteId;
            continue;
        }

        $expected = hash('sha256', $vote['student_reg_no'] . "-" . $vote['position_id'] . "-" . $vote['candidate_id'] . "-" . $voteId);
        if ($expected !== $integrityRecord['vote_hash']) {
            $issues[] = $voteId;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tamper Check</title>
</head>
<body>
    <h2>Tamper Check</h2>
    <?php if (!db_is_available()): ?>
        <p><?= htmlspecialchars(db_error_message()) ?></p>
    <?php elseif (empty($issues)): ?>
        <p>No tampering detected.</p>
    <?php else: ?>
        <p>Potential tampering found in vote IDs: <?= implode(', ', $issues) ?></p>
    <?php endif; ?>
    <a href="../frontend/admin_dashboard.php" class="button button-secondary">Return to Dashboard</a>
</body>
</html>
