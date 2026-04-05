# MongoDB Connection Verification

Create this file at the project root: `test_mongodb_connection.php`

Then access it via your browser to verify MongoDB is properly connected.

---

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>MongoDB Connection Test</h1>";
echo "<hr>";

// Check if MongoDB extension is loaded
echo "<h3>1. MongoDB Extension Check</h3>";
if (extension_loaded('mongodb')) {
    echo "<span style='color: green;'>✅ MongoDB extension is installed and enabled</span><br>";
} else {
    echo "<span style='color: red;'>❌ MongoDB extension is NOT installed</span><br>";
    echo "Install it with: <code>pecl install mongodb</code> or <code>composer require mongodb/mongodb</code><br>";
}

echo "<hr>";

// Check if Composer autoload exists
echo "<h3>2. Composer Autoload Check</h3>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<span style='color: green;'>✅ Composer vendor directory exists</span><br>";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "<span style='color: orange;'>⚠️ vendor/autoload.php not found</span><br>";
    echo "Run: <code>composer install</code><br>";
    echo "Attempting to continue without autoload...<br>";
}

echo "<hr>";

// Test MongoDB connection
echo "<h3>3. MongoDB Connection Test</h3>";

try {
    // Include the database configuration
    include __DIR__ . '/backend/db.php';
    
    // Try to connect and ping
    $client = new MongoDB\Client(
        getenv('MONGO_URI') ?: 'mongodb://localhost:27017'
    );
    $client->admin->command(['ping' => 1]);
    
    echo "<span style='color: green;'>✅ Connected to MongoDB successfully!</span><br>";
    
    // Check database
    $dbname = getenv('MONGO_DB') ?: 'voting_system';
    $db = $client->selectDatabase($dbname);
    
    echo "<span style='color: green;'>✅ Database selected: <code>$dbname</code></span><br>";
    
    echo "<hr>";
    
    // List collections
    echo "<h3>4. Existing Collections</h3>";
    $collections = $db->listCollections();
    
    if (count($collections) === 0) {
        echo "<span style='color: orange;'>⚠️ No collections found</span><br>";
        echo "Collections will be created when you first insert data into them.<br>";
    } else {
        echo "<span style='color: green;'>✅ Found " . count($collections) . " collection(s):</span><br>";
        echo "<ul>";
        foreach ($collections as $collection) {
            echo "<li><code>" . $collection->getName() . "</code></li>";
        }
        echo "</ul>";
    }
    
    echo "<hr>";
    
    // Test insert and query
    echo "<h3>5. Test Insert & Query</h3>";
    
    $testCollection = $db->selectCollection('test_connection');
    $testDoc = [
        'test' => true,
        'timestamp' => new MongoDB\BSON\UTCDateTime(time() * 1000),
        'message' => 'Connection verification test'
    ];
    
    $result = $testCollection->insertOne($testDoc);
    echo "<span style='color: green;'>✅ Successfully inserted test document</span><br>";
    echo "Inserted ID: <code>" . (string)$result->getInsertedId() . "</code><br>";
    
    // Query it back
    $verifyDoc = $testCollection->findOne();
    if ($verifyDoc) {
        echo "<span style='color: green;'>✅ Successfully retrieved test document</span><br>";
    }
    
    // Clean up
    $testCollection->deleteMany([]);
    echo "<span style='color: green;'>✅ Cleaned up test data</span><br>";
    
    echo "<hr>";
    
    // Recommendations
    echo "<h3>6. Recommendations</h3>";
    echo "<ul>";
    echo "<li>Create unique indexes for better performance:</li>";
    echo "<pre>";
    echo "db.students.createIndex({ 'reg_no': 1 }, { unique: true })\n";
    echo "db.students.createIndex({ 'email': 1 }, { unique: true })\n";
    echo "db.votes.createIndex({ 'student_reg_no': 1, 'position_id': 1 }, { unique: true })\n";
    echo "db.positions.createIndex({ 'position_name': 1 }, { unique: true })\n";
    echo "</pre>";
    echo "<li>Change admin password: Update <code>backend/admin_login.php</code></li>";
    echo "<li>Set environment variables for production</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ All checks passed! System is ready to use.</h2>";
    echo "<p><a href='index.php'>Go to application home</a></p>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>❌ MongoDB Connection Failed</span><br>";
    echo "<strong>Error:</strong><br>";
    echo "<code>" . htmlspecialchars($e->getMessage()) . "</code><br>";
    
    echo "<hr>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Is MongoDB running?</strong>";
    echo "<ul>";
    echo "<li>Windows: <code>net start MongoDB</code></li>";
    echo "<li>Linux/Mac: <code>brew services start mongodb-community</code></li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Is the MongoDB extension installed?</strong>";
    echo "<ul>";
    echo "<li><code>php -m | grep mongodb</code></li>";
    echo "<li>Or check phpinfo() for 'mongodb'</li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Check MONGO_URI:</strong>";
    echo "<ul>";
    echo "<li>Default: mongodb://localhost:27017</li>";
    echo "<li>Check firewall isn't blocking port 27017</li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Install MongoDB PHP Driver:</strong>";
    echo "<ul>";
    echo "<li><code>composer require mongodb/mongodb</code></li>";
    echo "<li>Or: <code>pecl install mongodb</code></li>";
    echo "</ul>";
    echo "</li>";
    echo "</ol>";
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    code {
        background-color: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
    pre {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 3px;
        overflow-x: auto;
        border-left: 3px solid #666;
    }
    h1, h2, h3 {
        color: #333;
    }
    ul, ol {
        line-height: 1.8;
    }
</style>
```

---

## How to Use

1. **Save the above code** as `test_mongodb_connection.php` in your project root
2. **Access it** via browser: `http://localhost/Online_%20voting_system/test_mongodb_connection.php`
3. **Review the results** to identify any issues

---

## What It Tests

✅ MongoDB PHP extension installation  
✅ Composer autoload  
✅ MongoDB server connection  
✅ Database connectivity  
✅ Collections status  
✅ Insert/Query operations  

---

## Expected Output

If everything is working, you should see:
- ✅ All green checkmarks
- List of existing collections
- Message: "All checks passed!"

---

## If You See Errors

**Error: "MongoDB extension is NOT installed"**
```bash
composer require mongodb/mongodb
# OR
pecl install mongodb
```

**Error: "Connected to MongoDB failed"**
- Ensure MongoDB service is running
- Check connection string in `backend/db.php`

**Error: "vendor/autoload.php not found"**
```bash
composer install
```

---

## Next Steps

After verification:

1. Delete the test file: `test_mongodb_connection.php`
2. Go to the application: `http://localhost/Online_%20voting_system/`
3. Start testing the voting system!

---

**Good luck! Your MongoDB voting system is ready to go! 🚀**
