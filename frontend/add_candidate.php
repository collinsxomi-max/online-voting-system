<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

include '../backend/db.php';

$positions = [];
foreach ($conn->selectCollection('positions')->find([], ['sort' => ['position_name' => 1]]) as $pos) {
    $positions[] = [
        '_id' => (string)$pos['_id'],
        'position_name' => $pos['position_name']
    ];
}

$candidates = [];
foreach ($conn->selectCollection('candidates')->find([], ['sort' => ['position_id' => 1, 'name' => 1]]) as $cand) {
    // Find the position name for this candidate
    $positionId = $cand['position_id'];
    if (is_string($positionId)) {
        $positionId = new \MongoDB\BSON\ObjectId($positionId);
    }
    $pos = $conn->selectCollection('positions')->findOne(['_id' => $positionId]);
    $candidates[] = [
        '_id' => (string)$cand['_id'],
        'name' => $cand['name'],
        'position_name' => $pos ? $pos['position_name'] : 'Unknown'
    ];
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard">
  <?php include '../includes/sidebar_admin.php'; ?>

  <section class="panel">
    <h2>Manage Candidates</h2>

    <form action="<?= $baseUrl ?>/backend/add_candidate.php" method="post">
      <div class="form-group">
        <label for="name">Name</label>
        <input id="name" class="form-control" type="text" name="name" required>
      </div>

      <div class="form-group">
        <label for="position_id">Position</label>
        <select id="position_id" class="form-control" name="position_id" required>
          <option value="">Select a position</option>
          <?php foreach ($positions as $position): ?>
            <option value="<?= $position['_id'] ?>"><?= htmlspecialchars($position['position_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="button button-primary" type="submit">Add Candidate</button>
    </form>

    <?php if (!empty($candidates)): ?>
      <table class="data-table" style="margin-top: 22px;">
        <thead>
          <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($candidates as $candidate): ?>
            <tr>
              <td><?= htmlspecialchars($candidate['name']) ?></td>
              <td><?= htmlspecialchars($candidate['position_name']) ?></td>
              <td>
                <form method="post" action="<?= $baseUrl ?>/backend/delete_candidate.php" style="display:inline;">
                  <input type="hidden" name="candidate_id" value="<?= $candidate['_id'] ?>">
                  <button class="button button-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
