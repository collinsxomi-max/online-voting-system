<?php
include 'db.php';

$logs = [];
foreach ($conn->selectCollection('audit_log')->find([], ['sort' => ['_id' => -1]]) as $log) {
    $logs[] = [
        'log_id' => (string)$log['_id'],
        'timestamp' => isset($log['timestamp']) ? $log['timestamp']->toDateTime()->format('Y-m-d H:i:s') : 'N/A',
        'action' => $log['action'] ?? '',
        'user_id' => $log['user_id'] ?? 'system'
    ];
}

echo "<h2>Audit Log</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Timestamp</th><th>Action</th><th>User ID</th></tr>";

foreach ($logs as $row) {
    echo "<tr>";
    echo "<td>{$row['log_id']}</td>";
    echo "<td>{$row['timestamp']}</td>";
    echo "<td>{$row['action']}</td>";
    echo "<td>" . htmlspecialchars((string) $row['user_id']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
