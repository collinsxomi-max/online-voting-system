<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}
// Prevent students from accessing admin results
if (isset($_SESSION['student_id']) && !isset($_SESSION['admin'])) {
    header('Location: results.php');
    exit;
}

include '../backend/db.php';

$results = [];
$departmentWinners = [];
$pipeline = [
    [
        '$lookup' => [
            'from' => 'departments',
            'localField' => 'department_id',
            'foreignField' => 'department_id',
            'as' => 'department'
        ]
    ],
    [
        '$lookup' => [
            'from' => 'positions',
            'localField' => 'position_id',
            'foreignField' => 'position_id',
            'as' => 'position'
        ]
    ],
    [
        '$lookup' => [
            'from' => 'votes',
            'localField' => 'candidate_id',
            'foreignField' => 'candidate_id',
            'as' => 'votes'
        ]
    ],
    [
        '$addFields' => [
            'department_name' => ['$arrayElemAt' => ['$department.name', 0]],
            'position_name' => ['$arrayElemAt' => ['$position.position_name', 0]],
            'total_votes' => ['$size' => '$votes']
        ]
    ],
    [
        '$sort' => [
            'department_name' => 1,
            'position_name' => 1,
            'total_votes' => -1,
            'name' => 1
        ]
    ]
];

$cursor = db_collection('candidates')->aggregate($pipeline);

$departmentWinners = [];
foreach ($cursor as $doc) {
    $row = db_to_array($doc);
    $dept = $row['department_name'];
    $position = $row['position_name'];
    $row['department'] = $dept;
    $row['candidate_name'] = $row['name'];
    $results[$dept][$position][] = $row;

    if (!isset($departmentWinners[$dept][$position]) || $row['total_votes'] > $departmentWinners[$dept][$position]['total_votes']) {
        $departmentWinners[$dept][$position] = $row;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard">
  <?php include '../includes/sidebar_admin.php'; ?>

  <section class="panel">
    <h2>Admin Results</h2>
    <p>Final winners per department and position. Export full results or winners as CSV.</p>

    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
      <a href="../backend/export_results.php?type=full" class="button button-primary">Export Full Results CSV</a>
      <a href="../backend/export_results.php?type=winners" class="button button-secondary">Export Winners CSV</a>
    </div>

    <?php if (empty($results)): ?>
      <div class="alert error">
        <span class="icon">⚠️</span>
        <span>No result data available yet.</span>
      </div>
    <?php else: ?>
      <?php foreach ($results as $department => $positions): ?>
        <div class="card" style="margin-bottom: 18px;">
          <h3 style="margin-top: 0;"><?= htmlspecialchars($department) ?></h3>

          <?php foreach ($positions as $position => $candidates): ?>
            <div class="results-group" style="margin-bottom: 16px;">
              <strong><?= htmlspecialchars($position) ?></strong>
              <div class="results-grid" style="margin-top: 8px;">
                <?php foreach ($candidates as $candidate): ?>
                  <?php $isWinner = isset($departmentWinners[$department][$position]) && $departmentWinners[$department][$position]['candidate_id'] === $candidate['candidate_id']; ?>
                  <div class="result-row<?= $isWinner ? ' winner-row' : '' ?>">
                    <div class="result-info">
                      <div class="result-name"><?= htmlspecialchars($candidate['candidate_name']) ?></div>
                      <div class="result-meta"><?= (int)$candidate['total_votes'] ?> votes</div>
                    </div>
                    <?php if ($isWinner): ?>
                      <span class="status-pill status-active">Winner</span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
