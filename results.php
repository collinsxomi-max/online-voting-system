<?php
include __DIR__ . '/backend/db.php';
include __DIR__ . '/includes/header.php';

$results = [];
$positionTotals = [];

$positionsCollection = $conn->selectCollection('positions');
$candidatesCollection = $conn->selectCollection('candidates');
$votesCollection = $conn->selectCollection('votes');

foreach ($positionsCollection->find() as $position) {
    $posId = $position['_id'];
    $posName = $position['position_name'];
    
    $candidates = $candidatesCollection->find(['position_id' => $posId]);
    
    foreach ($candidates as $candidate) {
        $candId = $candidate['_id'];
        $totalVotes = $votesCollection->countDocuments(['candidate_id' => $candId]);
        
        $results[$posName][] = [
            'candidate_id' => (string)$candId,
            'name' => $candidate['name'],
            'position_name' => $posName,
            'total_votes' => $totalVotes
        ];
        
        $positionTotals[$posName] = ($positionTotals[$posName] ?? 0) + $totalVotes;
    }
    
    // Sort by votes descending
    usort($results[$posName], function($a, $b) {
        return $b['total_votes'] - $a['total_votes'];
    });
}
?>

<div class="dashboard">
  <?php include __DIR__ . '/includes/sidebar_student.php'; ?>

  <section class="panel">
    <h2>Election Results</h2>

    <?php foreach ($results as $position => $candidates): ?>
      <div class="card" style="margin-bottom: 18px;">
        <h3><?= htmlspecialchars($position) ?></h3>
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
      </div>
    <?php endforeach; ?>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
