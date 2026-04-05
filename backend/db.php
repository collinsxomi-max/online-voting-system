<?php
function readEnvironmentValue(string $key): ?string
{
    $envValue = getenv($key);
    if ($envValue !== false && $envValue !== '') {
        return $envValue;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }

    if (function_exists('apache_getenv')) {
        $apacheValue = apache_getenv($key, true);
        if ($apacheValue !== false && $apacheValue !== '') {
            return $apacheValue;
        }
    }

    return null;
}

function getMongoConfigValue(array $config, string $key, array $envKeys = []): ?string
{
    if (array_key_exists($key, $config) && $config[$key] !== '') {
        return $config[$key];
    }

    foreach ($envKeys as $envKey) {
        $envValue = readEnvironmentValue($envKey);
        if ($envValue !== null) {
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

function dbFailureModeEnabled(): bool
{
    return defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE;
}

function setDatabaseUnavailable(string $message, ?string $details = null): void
{
    global $conn, $dbAvailable, $dbError;

    $conn = null;
    $dbAvailable = false;
    $dbError = $message;

    if (dbFailureModeEnabled()) {
        return;
    }

    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $safeDetails = $details !== null ? htmlspecialchars($details, ENT_QUOTES, 'UTF-8') : '';

    die("<html><head><title>Database Error</title><style>body { font-family: Arial; margin: 40px; } .code { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 3px; font-family: monospace; } h2 { color: #333; }</style></head><body>" .
        "<h2>Database Connection Failed</h2>" .
        "<p><strong>Error:</strong> {$safeMessage}</p>" .
        ($safeDetails !== '' ? "<div class='code'>{$safeDetails}</div>" : '') .
        "<p><strong>Check:</strong></p>" .
        "<ul>" .
        "<li>The <code>mongodb</code> PHP extension is enabled on the server</li>" .
        "<li>Composer dependencies are installed</li>" .
        "<li><code>MONGO_URI</code> and <code>MONGO_DB</code> are configured in the deployment environment</li>" .
        "</ul>" .
        "</body></html>");
}

function db_is_available(): bool
{
    global $dbAvailable;
    return $dbAvailable === true;
}

function db_error_message(): string
{
    global $dbError;

    return is_string($dbError) && $dbError !== ''
        ? $dbError
        : 'The database connection is currently unavailable.';
}

$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = file_exists($localConfigFile) ? require $localConfigFile : [];
if (!is_array($localConfig)) {
    $localConfig = [];
}

$conn = null;
$dbAvailable = false;
$dbError = null;

$mongoExtensionLoaded = extension_loaded('mongodb');
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
$hasComposer = file_exists($composerAutoload);

if (!$mongoExtensionLoaded) {
    setDatabaseUnavailable('The MongoDB PHP extension is not installed or enabled.');
    return;
}

if (!$hasComposer) {
    setDatabaseUnavailable('Composer dependencies are missing. Run composer install during deployment.');
    return;
}

require $composerAutoload;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$mongoUri = getMongoConfigValue($localConfig, 'mongo_uri', ['MONGO_URI', 'MONGODB_URI', 'MONGODB_URL']) ?: 'mongodb://localhost:27017';
$dbname = getMongoConfigValue($localConfig, 'mongo_db', ['MONGO_DB', 'MONGODB_DB', 'MONGODB_DATABASE']) ?: inferMongoDatabaseName($mongoUri) ?: 'voting_system';

try {
    $client = new Client($mongoUri);
    $conn = $client->selectDatabase($dbname);
    $conn->command(['ping' => 1]);
    $dbAvailable = true;
} catch (\Throwable $e) {
    setDatabaseUnavailable(
        'Unable to connect to MongoDB. Verify the deployment environment variables and Atlas network access.',
        $e->getMessage()
    );
    return;
}

if (!function_exists('log_action')) {
    function log_action($conn, $action, $user_id = 'system') {
        if (!$conn) {
            return;
        }

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
