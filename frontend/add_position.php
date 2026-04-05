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
        'position_name' => $pos['position_name'],
        'description' => $pos['description'] ?? ''
    ];
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard">
  <?php include '../includes/sidebar_admin.php'; ?>

  <section class="panel">
    <h2>Manage Election Positions</h2>

    <?php if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>">
        <span class="icon"><?= $flash['type'] === 'success' ? '&#10003;' : '&#9888;' ?></span>
        <span><?= htmlspecialchars($flash['message']) ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/backend/add_position.php" method="post">
      <div class="form-group">
        <label for="position_name">Position Name</label>
        <input id="position_name" class="form-control" type="text" name="position_name" required>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" class="form-control" name="description" rows="3"></textarea>
      </div>

      <button class="button button-primary" type="submit">Add Position</button>
    </form>

    <?php if (!empty($positions)): ?>
      <table class="data-table" style="margin-top: 22px;">
        <thead>
          <tr>
            <th>Position Name</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($positions as $position): ?>
            <tr>
              <td><?= htmlspecialchars($position['position_name']) ?></td>
              <td><?= htmlspecialchars($position['description'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
