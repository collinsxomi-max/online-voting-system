# MongoDB Setup Guide for Voting System

## Prerequisites
- MongoDB installed and running (download from https://www.mongodb.com/try/download/community)
- PHP 7.4+ with MongoDB extension installed

---

## Step 1: Install MongoDB PHP Driver

### Option A: Using PECL (Recommended)
```bash
pecl install mongodb
```

### Option B: Manual Installation
1. Download the MongoDB PHP driver from: https://pecl.php.net/package/mongodb
2. Extract and follow the installation instructions

### Option C: Using PHP Package Manager (Composer)
```bash
composer require mongodb/mongodb
```

---

## Step 2: Enable MongoDB Extension in PHP

### Windows (XAMPP):
1. Open `C:\xampp\php\php.ini`
2. Find the `;extension=mongodb` line and uncomment it:
   ```
   extension=mongodb
   ```
3. Restart Apache

### Verify Installation:
```bash
php -m | grep mongodb
```

Or create a test file with:
```php
<?php
phpinfo();
?>
```
Search for "MongoDB" in the output.

---

## Step 3: Configure MongoDB Connection

Edit `backend/db.php` and set your MongoDB URI via environment variables or in the file:

```php
$mongoUri = getenv('MONGO_URI') ?: 'mongodb://localhost:27017';
$dbname = getenv('MONGO_DB') ?: 'voting_system';
```

### Set Environment Variables (Optional):
```bash
# Windows
set MONGO_URI=mongodb://localhost:27017
set MONGO_DB=voting_system

# Linux/Mac
export MONGO_URI=mongodb://localhost:27017
export MONGO_DB=voting_system
```

---

## Step 4: Ensure MongoDB Service is Running

### Windows:
```bash
# Check if MongoDB is installed and running
sc query MongoDB

# Start MongoDB if not running
net start MongoDB
```

### Linux/Mac:
```bash
# Start MongoDB
brew services start mongodb-community

# Or manually
mongod
```

---

## Step 5: Verify MongoDB Connection

Create a test file at the project root:

```php
<?php
require 'vendor/autoload.php';

try {
    $client = new MongoDB\Client('mongodb://localhost:27017');
    $client->admin->command(['ping' => 1]);
    echo "MongoDB connection successful!";
} catch (Exception $e) {
    echo "MongoDB connection failed: " . $e->getMessage();
}
?>
```

Access via browser: `http://localhost/Online_%20voting_system/test-mongo.php`

---

## Step 6: Initialize Database Collections

MongoDB doesn't require explicit database creation (it's created on first write). However, you can optionally create indexes:

```bash
mongosh
use voting_system
db.students.createIndex({ "reg_no": 1 }, { unique: true })
db.students.createIndex({ "email": 1 }, { unique: true })
db.votes.createIndex({ "student_reg_no": 1, "position_id": 1 }, { unique: true })
db.positions.createIndex({ "position_name": 1 }, { unique: true })
```

---

## Step 7: Install PHP Dependencies (via Composer)

If not already installed, install Composer from https://getcomposer.org

Then run:
```bash
cd Online_voting_system
composer require mongodb/mongodb
```

Or create a `composer.json`:
```json
{
    "require": {
        "mongodb/mongodb": "^1.8"
    }
}
```

Then run: `composer install`

---

## Troubleshooting

### Error: "Class 'MongoDB\Client' not found"
- Ensure MongoDB PHP extension is installed and enabled
- Run: `composer require mongodb/mongodb`
- Restart your server

### Error: "MongoDB connection failed"
- Ensure MongoDB service is running
- Check URI: default is `mongodb://localhost:27017`
- For remote MongoDB, update `MONGO_URI` in `backend/db.php`

### MongoDB Not Starting
- Check MongoDB logs
- Ensure port 27017 is not in use
- Run: `mongod --version` to verify installation

---

## Testing the Application

1. **Register**: http://localhost/Online_%20voting_system/frontend/register.php
2. **Login**: http://localhost/Online_%20voting_system/frontend/login.php
3. **Admin Panel**: http://localhost/Online_%20voting_system/frontend/admin_login.php
4. **Vote**: http://localhost/Online_%20voting_system/vote.php
5. **Results**: http://localhost/Online_%20voting_system/results.php

---

## Default Admin Credentials

Username: `admin`
Password: `change-this-admin-password` (as set in `backend/admin_login.php`)

**⚠️ Change these credentials in production!**

---

## MongoDB Collections Structure

```javascript
// students
{
  "_id": ObjectId,
  "reg_no": String (unique),
  "full_name": String,
  "email": String (unique),
  "password_hash": String,
  "created_at": ISODate
}

// positions
{
  "_id": ObjectId,
  "position_name": String (unique),
  "description": String,
  "created_at": ISODate
}

// candidates
{
  "_id": ObjectId,
  "name": String,
  "position_id": ObjectId,
  "manifesto": String (optional)
}

// votes
{
  "_id": ObjectId,
  "student_reg_no": String,
  "candidate_id": ObjectId,
  "position_id": ObjectId,
  "vote_time": ISODate
}

// integrity
{
  "_id": ObjectId,
  "vote_id": String,
  "vote_hash": String
}

// audit_log
{
  "_id": ObjectId,
  "action": String,
  "user_id": String,
  "timestamp": ISODate
}
```

---

## Next Steps

1. Verify database connection works
2. Create admin account (optional - hardcoded in admin_login.php)
3. Add election positions via admin panel
4. Add candidates via admin panel
5. Students register and vote

All done! Your voting system is now using MongoDB! 🎉
