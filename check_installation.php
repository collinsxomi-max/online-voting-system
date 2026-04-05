<?php
define('ALLOW_DB_FAILURE', true);
require __DIR__ . '/backend/db.php';

function envLabel(array $names): string
{
    return implode(' or ', array_map(static fn($name) => '$' . $name, $names));
}

$mongoUriNames = ['MONGO_URI', 'MONGODB_URI', 'MONGODB_URL'];
$mongoDbNames = ['MONGO_DB', 'MONGODB_DB', 'MONGODB_DATABASE'];

$phpVersionOk = version_compare(PHP_VERSION, '8.1.0', '>=');
$mongoExtensionLoaded = extension_loaded('mongodb');
$vendorExists = file_exists(__DIR__ . '/vendor/autoload.php');
$mongoUri = null;
$mongoDb = null;

foreach ($mongoUriNames as $name) {
    $value = readEnvironmentValue($name);
    if ($value !== null) {
        $mongoUri = $value;
        break;
    }
}

foreach ($mongoDbNames as $name) {
    $value = readEnvironmentValue($name);
    if ($value !== null) {
        $mongoDb = $value;
        break;
    }
}

$checks = [];

$checks['PHP Version'] = [
    'required' => '8.1+',
    'current' => PHP_VERSION,
    'status' => $phpVersionOk ? 'PASS' : 'FAIL',
    'fix' => $phpVersionOk ? null : 'Upgrade the server to PHP 8.1 or newer.'
];

$checks['MongoDB Extension'] = [
    'required' => 'Installed and enabled',
    'current' => $mongoExtensionLoaded ? 'Installed' : 'Missing',
    'status' => $mongoExtensionLoaded ? 'PASS' : 'FAIL',
    'fix' => $mongoExtensionLoaded ? null : 'Install and enable the PHP mongodb extension on the deployment server.'
];

$checks['Composer Dependencies'] = [
    'required' => 'vendor/autoload.php present',
    'current' => $vendorExists ? 'Installed' : 'Missing',
    'status' => $vendorExists ? 'PASS' : 'FAIL',
    'fix' => $vendorExists ? null : 'Run composer install --no-dev on the deployment server during build/release.'
];

$checks['Mongo URI Env'] = [
    'required' => envLabel($mongoUriNames),
    'current' => $mongoUri !== null ? 'Configured' : 'Missing',
    'status' => $mongoUri !== null ? 'PASS' : 'FAIL',
    'fix' => $mongoUri !== null ? null : 'Add the MongoDB Atlas connection string as an environment variable.'
];

$checks['Mongo DB Env'] = [
    'required' => envLabel($mongoDbNames),
    'current' => $mongoDb !== null ? $mongoDb : 'Not explicitly set',
    'status' => $mongoDb !== null ? 'PASS' : 'WARN',
    'fix' => $mongoDb !== null ? null : 'Optional, but recommended. Set the database name to voting-system.'
];

$checks['Atlas Connection'] = [
    'required' => 'Ping to MongoDB Atlas succeeds',
    'current' => db_is_available() ? 'Connected successfully' : db_error_message(),
    'status' => db_is_available() ? 'PASS' : 'FAIL',
    'fix' => db_is_available() ? null : 'Verify Atlas IP/network access, environment variables, and that the mongodb extension is available in web PHP.'
];

$failures = array_filter($checks, static fn($check) => $check['status'] === 'FAIL');
$warnings = array_filter($checks, static fn($check) => $check['status'] === 'WARN');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System Deployment Check</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            max-width: 960px;
            margin: 0 auto;
            padding: 24px;
            background: #f3f6fb;
            color: #122033;
        }

        .hero,
        .panel,
        .item {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 14px 30px rgba(17, 24, 39, 0.08);
        }

        .hero {
            padding: 28px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: #fff;
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .hero p {
            margin: 0;
            opacity: 0.92;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .panel {
            padding: 18px 20px;
        }

        .panel strong {
            display: block;
            font-size: 26px;
            margin-top: 6px;
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        .item {
            padding: 18px 20px;
            border-left: 6px solid #cbd5e1;
        }

        .item.pass { border-left-color: #2e7d32; }
        .item.fail { border-left-color: #c62828; }
        .item.warn { border-left-color: #ef6c00; }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
        }

        .name {
            font-weight: 700;
            font-size: 18px;
        }

        .badge {
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.04em;
        }

        .badge.pass { background: #e8f5e9; color: #1b5e20; }
        .badge.fail { background: #ffebee; color: #b71c1c; }
        .badge.warn { background: #fff3e0; color: #e65100; }

        .meta {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 8px 16px;
            margin-top: 14px;
            font-size: 14px;
        }

        .label {
            color: #546274;
            font-weight: 600;
        }

        .value {
            word-break: break-word;
        }

        code {
            background: #eef2f7;
            padding: 2px 6px;
            border-radius: 6px;
        }

        ul {
            margin: 10px 0 0 20px;
        }
    </style>
</head>
<body>
    <section class="hero">
        <h1>Online Voting Deployment Check</h1>
        <p>This page verifies the same MongoDB setup the live app uses for registration and admin login.</p>
    </section>

    <section class="summary">
        <div class="panel">
            Status
            <strong><?= empty($failures) ? 'Healthy' : 'Action Needed' ?></strong>
        </div>
        <div class="panel">
            Failed Checks
            <strong><?= count($failures) ?></strong>
        </div>
        <div class="panel">
            Warnings
            <strong><?= count($warnings) ?></strong>
        </div>
    </section>

    <section class="grid">
        <?php foreach ($checks as $name => $check): ?>
            <?php $statusClass = strtolower($check['status']); ?>
            <div class="item <?= htmlspecialchars($statusClass) ?>">
                <div class="row">
                    <div class="name"><?= htmlspecialchars($name) ?></div>
                    <div class="badge <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($check['status']) ?></div>
                </div>
                <div class="meta">
                    <div class="label">Required</div>
                    <div class="value"><?= htmlspecialchars($check['required']) ?></div>
                    <div class="label">Current</div>
                    <div class="value"><?= htmlspecialchars($check['current']) ?></div>
                    <?php if (!empty($check['fix'])): ?>
                        <div class="label">Fix</div>
                        <div class="value"><?= htmlspecialchars($check['fix']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="panel" style="margin-top: 20px;">
        <h2 style="margin-top: 0;">Most Likely Deployment Fix</h2>
        <ul>
            <li>Set <code>MONGO_URI</code> to your Atlas URI and <code>MONGO_DB</code> to <code>voting-system</code>.</li>
            <li>Run <code>composer install --no-dev</code> on the server so <code>vendor/autoload.php</code> exists.</li>
            <li>Make sure the server PHP runtime has the <code>mongodb</code> extension enabled.</li>
            <li>Add your deployment server IP to MongoDB Atlas Network Access, or temporarily allow <code>0.0.0.0/0</code> while testing.</li>
        </ul>
    </section>
</body>
</html>
