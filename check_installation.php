<?php
/**
 * MongoDB Installation Checker
 * 
 * This script checks if your system is ready for the voting system
 * and provides step-by-step instructions to fix any issues
 */

$checks = [];

// 1. Check PHP Version
$checks['PHP Version'] = [
    'required' => '8.0+',
    'current' => PHP_VERSION,
    'status' => version_compare(PHP_VERSION, '8.0.0', '>=') ? '✅' : '❌',
    'fix' => version_compare(PHP_VERSION, '8.0.0', '>=') ? null : 'Download newer XAMPP: https://www.apachefriends.org'
];

// 2. Check MongoDB Extension
$checks['MongoDB Extension'] = [
    'required' => 'Installed',
    'current' => extension_loaded('mongodb') ? 'Installed' : 'NOT FOUND',
    'status' => extension_loaded('mongodb') ? '✅' : '❌',
    'fix' => !extension_loaded('mongodb') ? 'Follow INSTALL_NOW.md - Step 1' : null
];

// 3. Check Composer Installation
$composer_exists = file_exists(__DIR__ . '/composer.phar');
$checks['Composer'] = [
    'required' => 'Installed',
    'current' => $composer_exists ? 'Installed' : 'NOT FOUND',
    'status' => $composer_exists ? '✅' : '❌',
    'fix' => !$composer_exists ? 'Download: https://getcomposer.org/download/' : null
];

// 4. Check Vendor Directory
$vendor_exists = file_exists(__DIR__ . '/vendor/autoload.php');
$checks['Vendor/Autoload'] = [
    'required' => 'vendor/autoload.php',
    'current' => $vendor_exists ? 'Found' : 'NOT FOUND',
    'status' => $vendor_exists ? '✅' : '❌',
    'fix' => !$vendor_exists ? 'Run: composer install (see INSTALL_NOW.md)' : null
];

// 5. Check MongoDB Connection (if possible)
if (extension_loaded('mongodb') && $vendor_exists) {
    try {
        require __DIR__ . '/vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $client->selectDatabase('local')->command(['ping' => 1]);
        $checks['MongoDB Server'] = [
            'required' => 'Running',
            'current' => 'Connected',
            'status' => '✅',
            'fix' => null
        ];
    } catch (Exception $e) {
        $checks['MongoDB Server'] = [
            'required' => 'Running',
            'current' => 'Connection Failed: ' . $e->getMessage(),
            'status' => '❌',
            'fix' => 'Start MongoDB: Open cmd and run: net start MongoDB'
        ];
    }
} else {
    $checks['MongoDB Server'] = [
        'required' => 'Running',
        'current' => 'Cannot check yet (extension/vendor missing)',
        'status' => '⏳',
        'fix' => 'First complete steps above'
    ];
}

// 6. Check Database Schema
$checks['Database Schema'] = [
    'required' => 'collections created',
    'current' => $vendor_exists ? 'Pending verification' : 'Cannot check',
    'status' => '⏳',
    'fix' => 'Automatically created on first use'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - Installation Checker</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 1.1em;
            opacity: 0.9;
        }
        .check-grid {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }
        .check-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 5px solid #ddd;
        }
        .check-item.pass {
            border-left-color: #4caf50;
            background: #f1f8f4;
        }
        .check-item.fail {
            border-left-color: #f44336;
            background: #fef5f5;
        }
        .check-item.warning {
            border-left-color: #ff9800;
            background: #fff8f0;
        }
        .check-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .check-name {
            font-weight: bold;
            font-size: 1.1em;
        }
        .check-status {
            font-size: 2em;
            margin: 0;
        }
        .check-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .detail {
            font-size: 0.9em;
        }
        .detail-label {
            color: #666;
            font-size: 0.8em;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .detail-value {
            font-family: 'Courier New', monospace;
            color: #333;
            font-weight: 500;
            word-break: break-all;
        }
        .fix {
            background: #fff3cd;
            padding: 10px 15px;
            border-radius: 4px;
            margin-top: 10px;
            margin-left: -20px;
            margin-right: -20px;
            margin-bottom: -20px;
            padding-bottom: 10px;
            border-radius: 0 0 8px 8px;
        }
        .fix-title {
            color: #856404;
            font-weight: bold;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .fix-text {
            color: #856404;
            margin: 0;
            padding: 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .summary h2 {
            margin-top: 0;
        }
        .summary-pass {
            color: #4caf50;
            font-size: 1.2em;
        }
        .summary-fail {
            color: #f44336;
            font-size: 1.2em;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🗳️ Online Voting System</h1>
        <p>Installation & Configuration Checker</p>
    </div>

    <div class="check-grid">
        <?php foreach ($checks as $name => $check): ?>
            <?php
                $status_class = 'plain';
                if ($check['status'] === '✅') $status_class = 'pass';
                elseif ($check['status'] === '❌') $status_class = 'fail';
                elseif ($check['status'] === '⏳') $status_class = 'warning';
            ?>
            <div class="check-item <?php echo $status_class; ?>">
                <div class="check-header">
                    <div class="check-name"><?php echo htmlspecialchars($name); ?></div>
                    <div class="check-status"><?php echo $check['status']; ?></div>
                </div>
                <div class="check-details">
                    <div class="detail">
                        <div class="detail-label">Required</div>
                        <div class="detail-value"><?php echo htmlspecialchars($check['required']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="detail-label">Current</div>
                        <div class="detail-value"><?php echo htmlspecialchars($check['current']); ?></div>
                    </div>
                </div>
                <?php if ($check['fix']): ?>
                    <div class="fix">
                        <div class="fix-title">ℹ️ Action Required:</div>
                        <p class="fix-text"><?php echo $check['fix']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="summary">
        <h2>📋 Next Steps</h2>
        <?php
            $failures = array_filter($checks, fn($c) => $c['status'] === '❌');
            $warnings = array_filter($checks, fn($c) => $c['status'] === '⏳');
        ?>
        
        <?php if (empty($failures)): ?>
            <p class="summary-pass"><strong>✅ You're all set!</strong></p>
            <p>Your voting system is ready to use. Open your browser and go to:</p>
            <p><code>http://localhost/Online_voting_system/</code></p>
        <?php else: ?>
            <p class="summary-fail"><strong>⚠️ Setup Required</strong></p>
            <p>You have <strong><?php echo count($failures); ?> issue(s)</strong> to fix before you can use the voting system.</p>
            <p><strong>Please follow the instructions above</strong>, or open the <code>INSTALL_NOW.md</code> file in your project root for detailed step-by-step guidance.</p>
            <p>The main issues are:</p>
            <ul>
                <?php foreach ($failures as $name => $check): ?>
                    <li><strong><?php echo htmlspecialchars($name); ?>:</strong> <?php echo htmlspecialchars($check['current']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</body>
</html>
