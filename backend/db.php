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

function resolveMongoConfigValue(array $config, string $key, array $envKeys = []): array
{
    if (array_key_exists($key, $config) && $config[$key] !== '') {
        return [
            'value' => $config[$key],
            'source' => 'local:' . $key,
        ];
    }

    foreach ($envKeys as $envKey) {
        $envValue = readEnvironmentValue($envKey);
        if ($envValue !== null) {
            return [
                'value' => $envValue,
                'source' => 'env:' . $envKey,
            ];
        }
    }

    return [
        'value' => null,
        'source' => null,
    ];
}

function getMongoConfigValue(array $config, string $key, array $envKeys = []): ?string
{
    return resolveMongoConfigValue($config, $key, $envKeys)['value'];
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

function db_config_diagnostics(): array
{
    global $dbConfigDiagnostics;

    return is_array($dbConfigDiagnostics)
        ? $dbConfigDiagnostics
        : [
            'uri_source' => null,
            'db_source' => null,
            'uri_configured' => false,
            'db_configured' => false,
        ];
}

$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = file_exists($localConfigFile) ? require $localConfigFile : [];
if (!is_array($localConfig)) {
    $localConfig = [];
}

$conn = null;
$dbAvailable = false;
$dbError = null;
$dbConfigDiagnostics = [
    'uri_source' => null,
    'db_source' => null,
    'uri_configured' => false,
    'db_configured' => false,
];

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

$mongoUriConfig = resolveMongoConfigValue($localConfig, 'mongo_uri', ['MONGO_URI', 'MONGODB_URI', 'MONGODB_URL']);
$mongoUri = $mongoUriConfig['value'] ?: 'mongodb://localhost:27017';

$mongoDbConfig = resolveMongoConfigValue($localConfig, 'mongo_db', ['MONGO_DB', 'MONGODB_DB', 'MONGODB_DATABASE']);
$inferredDbName = inferMongoDatabaseName($mongoUri);
$dbname = $mongoDbConfig['value'] ?: $inferredDbName ?: 'voting_system';

$dbConfigDiagnostics = [
    'uri_source' => $mongoUriConfig['source'] ?: 'default',
    'db_source' => $mongoDbConfig['source'] ?: ($inferredDbName !== null ? 'uri-path' : 'default'),
    'uri_configured' => $mongoUriConfig['value'] !== null,
    'db_configured' => $mongoDbConfig['value'] !== null,
];

try {
    $client = new Client($mongoUri);
    $conn = $client->selectDatabase($dbname);
    $conn->command(['ping' => 1]);
    $dbAvailable = true;
} catch (\Throwable $e) {
    $connectionMessage = $mongoUriConfig['value'] !== null
        ? 'Unable to connect to MongoDB. Verify the deployment environment variables and Atlas network access.'
        : 'Unable to connect to MongoDB. No connection string is configured, so the app fell back to mongodb://localhost:27017. Set MONGO_URI or create backend/config.local.php.';

    setDatabaseUnavailable(
        $connectionMessage,
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
