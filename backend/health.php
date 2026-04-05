<?php
header('Content-Type: text/plain; charset=utf-8');

echo "MONGODB_URI: " . getenv('MONGODB_URI') . "\n";
echo "MONGODB_DB: " . getenv('MONGODB_DB') . "\n";

include __DIR__ . '/db.php';

if (database_ready($conn)) {
    echo "MongoDB connected\n";
    exit;
}

echo "MongoDB unavailable";
if (!empty($db_error)) {
    echo ': ' . $db_error;
}
echo "\n";
