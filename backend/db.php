<?php
function getMongoConfigValue(array $config, string $key, ?string $envKey = null): ?string
{
    if (array_key_exists($key, $config) && $config[$key] !== '') {
        return $config[$key];
    }

    if ($envKey !== null) {
        $envValue = getenv($envKey);
        if ($envValue !== false && $envValue !== '') {
            return $envValue;
        }
    }

    return null;
}

function inferMongoDatabaseName(string $mongoUri): ?string
{
    $path = parse_url($mongoUri, PHP_URL_PATH);
    if (!is_string($path) || $path === '' || $path === '/') {
        return null;
    }

    $database = ltrim($path, '/');
    return $database !== '' ? $database : null;
}

$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = file_exists($localConfigFile) ? require $localConfigFile : [];
if (!is_array($localConfig)) {
    $localConfig = [];
}

// Check for MongoDB driver - either via Composer or PECL extension
$mongoExtensionLoaded = extension_loaded('mongodb');
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
$hasComposer = file_exists($composerAutoload);

if (!$mongoExtensionLoaded && !$hasComposer) {
    // Neither Composer nor PECL extension is available
    die("<html><head><title>Setup Required</title><style>body { font-family: Arial; margin: 40px; } .error { color: red; } .code { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 3px; font-family: monospace; } h2 { color: #333; }</style></head><body>" .
        "<h2>⚠️ MongoDB Setup Required</h2>" .
        "<p>MongoDB PHP driver is not installed.</p>" .
        "<p><strong>Choose one option:</strong></p>" .
        "<ol>" .
        "<li><strong>Recommended: Install via Composer</strong>" .
        "<div class='code'>cd c:\\xampp\\htdocs\\Online_voting_system<br>c:\\xampp\\php\\php.exe composer.phar install</div>" .
        "</li>" .
        "<li><strong>OR: Install MongoDB PECL extension</strong>" .
        "<div class='code'>pecl install mongodb</div>" .
        "Then enable in php.ini: extension=mongodb" .
        "</li>" .
        "</ol>" .
        "<p><a href='https://www.php.net/manual/en/mongodb.installation.php' target='_blank'>View MongoDB Installation Guide</a></p>" .
        "</body></html>");
}

// Load Composer if available
if ($hasComposer) {
    require $composerAutoload;
}

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$mongoUri = getMongoConfigValue($localConfig, 'mongo_uri', 'MONGO_URI') ?: 'mongodb://localhost:27017';
$dbname = getMongoConfigValue($localConfig, 'mongo_db', 'MONGO_DB') ?: inferMongoDatabaseName($mongoUri) ?: 'voting_system';

try {
    $client = new Client($mongoUri);
    $conn = $client->selectDatabase($dbname);
    
    // Test connection
    $conn->command(['ping' => 1]);
} catch (\Exception $e) {
    die("<html><head><title>Database Error</title><style>body { font-family: Arial; margin: 40px; } .error { color: red; } .code { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 3px; font-family: monospace; } h2 { color: #333; }</style></head><body>" .
        "<h2>❌ MongoDB Connection Failed</h2>" .
        "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
        "<p><strong>Ensure:</strong></p>" .
        "<ul>" .
        "<li>MongoDB service is running: <code>net start MongoDB</code></li>" .
        "<li>Connection string is correct: <code>" . htmlspecialchars($mongoUri) . "</code></li>" .
        "<li>Database is accessible</li>" .
        "</ul>" .
        "<p><a href='mailto:admin@example.com'>Contact Administrator</a></p>" .
        "</body></html>");
}

if (!function_exists('log_action')) {
    function log_action($conn, $action, $user_id = 'system') {
        try {
            $conn->selectCollection('audit_log')->insertOne([
                'action' => $action,
                'user_id' => $user_id,
                'timestamp' => new MongoDB\BSON\UTCDateTime(time() * 1000)
            ]);
        } catch (\Exception $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
?>
