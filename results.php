<?php
define('ALLOW_DB_FAILURE', true);
include __DIR__ . '/backend/db.php';
include __DIR__ . '/includes/header.php';

$results = [];
$positionTotals = [];

if (db_is_available()) {
    $positionsCollection = $conn->selectCollection('positions');
    $candidatesCollection = $conn->selectCollection('candidates');
    $votesCollection = $conn->selectCollection('votes');

    foreach ($positionsCollection->find([], ['sort' => ['position_name' => 1]]) as $position) {
        $posId = $position['_id'];
        $posName = $position['position_name'];
        $results[$posName] = [];

        foreach ($candidatesCollection->find(['position_id' => $posId], ['sort' => ['name' => 1]]) as $candidate) {
            $candId = $candidate['_id'];
            $totalVotes = $votesCollection->countDocuments([
                'candidate_id' => $candId,
                'position_id' => $posId,
            ]);

            $results[$posName][] = [
                'candidate_id' => (string) $candId,
                'name' => $candidate['name'],
                'position_name' => $posName,
                'total_votes' => $totalVotes
            ];

            $positionTotals[$posName] = ($positionTotals[$posName] ?? 0) + $totalVotes;
        }

        usort($results[$posName], function ($a, $b) {
            return $b['total_votes'] <=> $a['total_votes'];
        });
    }
}
?>

<div class="dashboard">
  <?php if (!empty($_SESSION['admin'])): ?>
    <?php include __DIR__ . '/includes/sidebar_admin.php'; ?>
  <?php elseif (!empty($_SESSION['student_reg_no'])): ?>
    <?php include __DIR__ . '/includes/sidebar_student.php'; ?>
  <?php endif; ?>

  <section class="panel">
    <h2>Election Results</h2>

    <?php if (!db_is_available()): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span><?= htmlspecialchars(db_error_message()) ?></span>
      </div>
    <?php elseif (empty($results)): ?>
      <div class="alert error">
        <span class="icon">&#9888;</span>
        <span>No election data is available yet.</span>
      </div>
    <?php else: ?>
    <?php foreach ($results as $position => $candidates): ?>
      <div class="card" style="margin-bottom: 18px;">
        <h3><?= htmlspecialchars($position) ?></h3>
        <?php if (empty($candidates)): ?>
          <div class="alert error">
            <span class="icon">&#9888;</span>
            <span>No candidates have been added for this position yet.</span>
          </div>
        <?php else: ?>
          <div class="results-grid">
            <?php foreach ($candidates as $row):
              $total = (int) $row['total_votes'];
              $denominator = max(1, $positionTotals[$position] ?? 0);
              $pct = $denominator ? round($total / $denominator * 100) : 0;
            ?>
              <div class="result-row">
                <div class="result-info">
                  <div class="result-name"><?= htmlspecialchars($row['name']) ?></div>
                  <div class="result-meta"><?= $total ?> votes &middot; <?= $pct ?>%</div>
                </div>
                <div class="result-bar">
                  <div class="result-bar-fill" style="width: <?= $pct ?>%;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
