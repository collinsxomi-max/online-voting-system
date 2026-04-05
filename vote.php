<?php
session_start();
if (!isset($_SESSION['student_reg_no'])) {
    header('Location: login.php');
    exit;
}

define('ALLOW_DB_FAILURE', true);
include __DIR__ . '/backend/db.php';

$studentRegNo = $_SESSION['student_reg_no'];

$positions = [];
$positionIds = [];
$votedPositionIds = [];
$remainingPositionIds = [];
$hasVoted = false;
$candidatesByPositionId = [];

if (db_is_available()) {
    $positionsCollection = $conn->selectCollection('positions');
    foreach ($positionsCollection->find([], ['sort' => ['position_name' => 1]]) as $position) {
        $positions[(string)$position['_id']] = $position['position_name'];
    }

    $positionIds = array_keys($positions);

    $votesCollection = $conn->selectCollection('votes');
    foreach ($votesCollection->find(['student_reg_no' => $studentRegNo], ['projection' => ['position_id' => 1]]) as $vote) {
        $posIdStr = (string)$vote['position_id'];
        if (!in_array($posIdStr, $votedPositionIds, true)) {
            $votedPositionIds[] = $posIdStr;
        }
    }

    $remainingPositionIds = array_values(array_diff($positionIds, $votedPositionIds));
    $hasVoted = !empty($positionIds) && empty($remainingPositionIds);

    $candidatesCollection = $conn->selectCollection('candidates');
    foreach ($candidatesCollection->find([], ['sort' => ['position_id' => 1, 'name' => 1]]) as $candidate) {
        $posId = (string)$candidate['position_id'];
        if (!isset($candidatesByPositionId[$posId])) {
            $candidatesByPositionId[$posId] = [];
        }
        $candidatesByPositionId[$posId][] = $candidate;
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="dashboard">
  <?php include __DIR__ . '/includes/sidebar_student.php'; ?>

  <section class="panel">
    <h2>Cast Your Vote</h2>

    <?php if (!db_is_available()): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars(db_error_message()) ?></span>
      </div>
    <?php elseif (empty($positions)): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span>No election positions are available yet.</span>
      </div>
    <?php elseif ($hasVoted): ?>
      <div class="alert success">
        <span class="icon">&#10003;</span>
        <span>You have already cast your vote for all positions.</span>
      </div>
    <?php elseif (empty($candidatesByPositionId)): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span>No candidates are currently available.</span>
      </div>
    <?php else: ?>
      <form action="<?= $baseUrl ?>/backend/vote.php" method="post" onsubmit="return confirmVote();">
        <?php foreach ($remainingPositionIds as $positionId): ?>
          <div class="card" style="margin-bottom: 18px;">
            <h3><?= htmlspecialchars($positions[$positionId]) ?></h3>
            <?php foreach ($candidatesByPositionId[$positionId] ?? [] as $candidate): ?>
              <div class="form-group">
                <label>
                  <input type="radio" name="candidate_id[<?= $positionId ?>]" value="<?= (string)$candidate['_id'] ?>" required>
                  <strong><?= htmlspecialchars($candidate['name']) ?></strong>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>

        <button class="button button-primary" type="submit">Submit Vote</button>
      </form>
    <?php endif; ?>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
