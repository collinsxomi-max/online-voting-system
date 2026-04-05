<?php
define('ALLOW_DB_FAILURE', true);
require __DIR__ . '/backend/db.php';
require __DIR__ . '/backend/database_setup.php';

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}

$result = null;
$error = null;

if (!db_is_available()) {
    $error = db_error_message();
} else {
    try {
        $result = initializeDatabase($conn);
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            max-width: 880px;
            margin: 0 auto;
            padding: 24px;
            background: #f3f6fb;
            color: #122033;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 14px 30px rgba(17, 24, 39, 0.08);
            padding: 24px;
            margin-bottom: 18px;
        }

        .ok {
            border-left: 6px solid #2e7d32;
        }

        .fail {
            border-left: 6px solid #c62828;
        }

        code {
            background: #eef2f7;
            padding: 2px 6px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>MongoDB Setup</h1>
        <p>This project uses one database, <code>voting-system</code>, with multiple collections.</p>
    </div>

    <?php if ($error !== null): ?>
        <div class="card fail">
            <h2>Setup failed</h2>
            <p><?= htmlspecialchars($error) ?></p>
            <p>Open <code><?= htmlspecialchars($baseUrl) ?>/check_installation.php</code> to see what is missing.</p>
        </div>
    <?php else: ?>
        <div class="card ok">
            <h2>Setup complete</h2>
            <p>Required collections and indexes are ready.</p>
            <p>Collections created: <?= !empty($result['created_collections']) ? htmlspecialchars(implode(', ', $result['created_collections'])) : 'None needed' ?></p>
            <p>Default positions added: <?= !empty($result['seeded_positions']) ? htmlspecialchars(implode(', ', $result['seeded_positions'])) : 'None needed' ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
