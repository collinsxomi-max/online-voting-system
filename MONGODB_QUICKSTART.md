# MongoDB Migration - Quick Start Guide

## ✅ What's Been Done

Your online voting system has been **fully migrated from MySQL to MongoDB**!

All database operations have been converted:
- ✅ MySQL prepared statements → MongoDB insertOne/find/update operations
- ✅ SQL JOINs → MongoDB queries with ObjectId references
- ✅ Integer IDs → MongoDB ObjectIds
- ✅ Audit logging → MongoDB collections
- ✅ Tamper detection → MongoDB integrity collection

---

## 🚀 Installation Steps

### Step 1: Install MongoDB PHP Driver

**For Windows (XAMPP):**
```bash
# Option A: Using Composer (Recommended)
cd c:\xampp\htdocs\Online_voting_system
composer install

# Option B: Manual PECL Installation
pecl install mongodb

# Then enable in php.ini:
# extension=mongodb
```

**For Linux/Mac:**
```bash
brew install mongodb-community
pecl install mongodb
```

### Step 2: Start MongoDB Service

**Windows:**
```bash
# If MongoDB is installed as a service
net start MongoDB

# Or manually
mongod
```

**Linux/Mac:**
```bash
brew services start mongodb-community
```

### Step 3: Verify Installation

Create a test file and access it via browser:
```php
<?php
require 'vendor/autoload.php';
$client = new MongoDB\Client('mongodb://localhost:27017');
$client->admin->command(['ping' => 1]);
echo "✅ MongoDB connection successful!";
?>
```

### Step 4: Create Required MongoDB Indexes (Optional but Recommended)

```bash
mongosh
use voting_system
db.students.createIndex({ "reg_no": 1 }, { unique: true })
db.students.createIndex({ "email": 1 }, { unique: true })
db.votes.createIndex({ "student_reg_no": 1, "position_id": 1 }, { unique: true })
db.positions.createIndex({ "position_name": 1 }, { unique: true })
exit
```

---

## 💻 Application Launch

1. **Start XAMPP** (or your web server)
2. **Start MongoDB** (port 27017)
3. **Access application**: `http://localhost/Online_%20voting_system/`

---

## 🧪 Testing the System

### Register a Student
1. Go to: `/frontend/register.php`
2. Fill in:
   - Registration Number: `STU001`
   - Full Name: `John Voter`
   - Email: `john@university.edu`
   - Password: `SecurePass123!`
3. Click "Register"

### Login as Student
1. Go to: `/frontend/login.php`
2. Use credentials from above
3. Access dashboard

### Setup Election (Admin)
1. Go to: `/frontend/admin_login.php`
2. Username: `admin`
3. Password: `change-this-admin-password`
4. Create positions, add candidates
5. Give the registration number to students for voting

### Cast a Vote
1. Login as student
2. Click "Go Vote"
3. Select candidates for each position
4. Submit vote

### View Results
1. Access: `/results.php`
2. See live voting results

### Check Audit Logs
1. Admin or student access: `/frontend/view_audit.php`
2. View all system actions

### Verify Vote Integrity
1. Admin access: `/backend/tamper_check.php`
2. Verify no votes have been tampered with

---

## 📁 MongoDB Collection Structure

Your data is organized in 6 collections:

### `students`
```json
{
  "_id": ObjectId("..."),
  "reg_no": "STU001",
  "full_name": "John Voter",
  "email": "john@university.edu",
  "password_hash": "$2y$10...",
  "created_at": ISODate("2025-04-01T10:30:00Z")
}
```

### `positions`
```json
{
  "_id": ObjectId("..."),
  "position_name": "President",
  "description": "Student body president",
  "created_at": ISODate("2025-04-01T10:30:00Z")
}
```

### `candidates`
```json
{
  "_id": ObjectId("..."),
  "name": "Jane Executive",
  "position_id": ObjectId("..."),  // Reference to positions._id
  "manifesto": "My election promises..."
}
```

### `votes`
```json
{
  "_id": ObjectId("..."),
  "student_reg_no": "STU001",
  "candidate_id": ObjectId("..."),  // Reference to candidates._id
  "position_id": ObjectId("..."),   // Reference to positions._id
  "vote_time": ISODate("2025-04-01T11:45:00Z")
}
```

### `integrity`
```json
{
  "_id": ObjectId("..."),
  "vote_id": "507f1f77bcf86cd799439011",  // String reference to votes._id
  "vote_hash": "a1b2c3d4e5f6..."         // SHA256 hash for tamper detection
}
```

### `audit_log`
```json
{
  "_id": ObjectId("..."),
  "action": "User logged in",
  "user_id": "STU001",
  "timestamp": ISODate("2025-04-01T11:50:00Z")
}
```

---

## ⚙️ Configuration

### Database Connection (`backend/db.php`)

Default configuration (localhost, no credentials):
```php
$mongoUri = getenv('MONGO_URI') ?: 'mongodb://localhost:27017';
$dbname = getenv('MONGO_DB') ?: 'voting_system';
```

### For Remote MongoDB

Set environment variables or edit `backend/db.php`:
```php
// Example for MongoDB Atlas
$mongoUri = 'mongodb+srv://username:password@cluster0.mongodb.net/?retryWrites=true&w=majority';
$dbname = 'voting_system';
```

### Admin Credentials (`backend/admin_login.php`)

⚠️ **CHANGE IN PRODUCTION!**
```php
$adminUser = getenv('ADMIN_USER') ?: 'admin';
$adminPass = getenv('ADMIN_PASS') ?: 'change-this-admin-password';
```

Set via environment variables:
```bash
set ADMIN_USER=your_admin_username
set ADMIN_PASS=your_secure_password
```

---

## 🔒 Security Notes

1. **Change admin password** before production
2. **Use encrypted MongoDB connection** for remote databases
3. **Enable MongoDB authentication** with username/password
4. **Set unique indexes** to prevent duplicate registrations
5. **Use HTTPS** in production
6. **Validate all inputs** (already implemented)

---

## 🐛 Troubleshooting

### MongoDB Connection Failed
```
Error: MongoDB connection failed. Ensure MongoDB is running...
```
**Fix:**
- Check MongoDB is running: `mongosh` 
- Default port: 27017
- Update MONGO_URI in backend/db.php

### Class 'MongoDB\Client' not found
```
Error: Class 'MongoDB\Client' not found
```
**Fix:**
- Install MongoDB driver: `composer require mongodb/mongodb`
- Enable extension in ph.ini: `extension=mongodb`
- Restart web server

### Vote submission fails
```
Error: Vote insertion failed
```
**Fix:**
- Ensure positions exist (admin must create them first)
- Check student is logged in
- Verify ObjectId conversion is working

### Duplicate entry error
```
Error: E11000 duplicate key error
```
**Fix:**
- Check unique indexes are set correctly
- Student can't vote twice for same position (expected)
- Email/registration number must be unique

---

## 📞 Support Files

- **Setup Guide**: `MONGODB_SETUP.md`
- **Composer Config**: `composer.json`
- **Database Config**: `backend/db.php`

---

## ✨ Features

✅ **Secure Student Registration**
- Bcrypt password hashing
- Unique email & registration number validation

✅ **Secure Voting**
- One vote per student per position
- Vote integrity verification with SHA256 hashing
- Tamper detection system

✅ **Live Results**
- Real-time vote counting
- Percentage calculations
- Visual result bars

✅ **Audit Trail**
- All actions logged (login, vote cast, etc.)
- Searchable audit log
- Admin access only

✅ **Admin Dashboard**
- Manage election positions
- Add/remove candidates
- View results and audit logs
- Verify vote integrity

---

## 🎉 You're All Set!

Your voting system is now powered by MongoDB and ready to run!

**Next:** Follow the testing steps above to verify everything works correctly.

**Questions?** Check individual files or `MONGODB_SETUP.md` for detailed information.
