# 🗳️ Online Voting System - QUICK START

## ⚡ Get Started in 3 Steps

### Step 1: Install MongoDB Extension
The MongoDB PHP extension is required. This is the ONLY manual step.

**Open:** [INSTALL_NOW.md](INSTALL_NOW.md)
**Follow:** Option 1 OR Option 2 (pick whichever is easier for you)

This takes about 5-10 minutes.

---

### Step 2: Run Automatic Setup
Once the MongoDB extension is installed, double-click:

**File:** `SETUP.bat`

This script will:
- ✅ Check PHP & Composer
- ✅ Download MongoDB PHP library
- ✅ Set up all dependencies

Takes 10-15 minutes. Let it finish!

---

### Step 3: Start and Access
1. **Start Apache** (XAMPP Control Panel → Start)
2. **Start MongoDB** (if running locally)
3. **Open browser:** `http://localhost/Online_voting_system/`

---

## ✅ Verify Installation

Open your browser and go to:
```
http://localhost/Online_voting_system/check_installation.php
```

This shows what's working and what needs fixing.

---

## 📚 Full Documentation

- **[INSTALL_NOW.md](INSTALL_NOW.md)** - Detailed installation steps with troubleshooting
- **[MONGODB_QUICKSTART.md](MONGODB_QUICKSTART.md)** - Feature overview
- **[MONGODB_SETUP.md](MONGODB_SETUP.md)** - Database setup guide
- **[MIGRATION_SUMMARY.md](MIGRATION_SUMMARY.md)** - Technical details of MySQL→MongoDB migration

---

## 🎯 Using the Voting System

### For Administrators:
1. Go to: `/frontend/admin_login.php`
2. Default credentials:
   - Username: `admin`
   - Password: `change-this-admin-password`
3. **IMPORTANT:** Change this password immediately!
4. Create election positions
5. Add candidates for each position

### For Students:
1. Go to: `/frontend/` or `/`
2. Click "Register"
3. Create account with registration number
4. Login
5. Vote for candidates
6. View live results

### View Election Results:
- Go to: `/results.php`
- See live voting results

### View Audit Log:
- Go to: `/frontend/view_audit.php`
- See all system actions (requires admin)

---

## ⚠️ Important Security Notes

1. **Change Admin Password!**
   - Edit: `backend/admin_login.php`
   - Find the login validation (lines 7-8)
   - Change username and password

2. **Change Default Positions**
   - Default positions are set in MongoDB
   - Customize through admin interface

3. **Use HTTPS in Production**
   - These instructions are for development only
   - For production, use a real domain with SSL/TLS

---

## 🆘 Need Help?

### Installation Stuck?
- Check: [check_installation.php](check_installation.php) in your browser
- It shows exactly what's working and what's not

### Files Won't Load?
- Did you complete Step 1? (MongoDB extension is critical)
- Is Apache running? (Check XAMPP Control Panel)
- Did the SETUP.bat finish? (Don't interrupt it)

### Connection Errors?
- Is MongoDB running? Try: `net start MongoDB`
- Check the documentation files above

---

## 🚀 What's Different From Original?

This system has been **completely migrated from MySQL to MongoDB:**
- Faster database queries
- Better scalability
- Modern document-based storage

All functionality works the same way from a user perspective!

---

## 📞 Status

- ✅ Code: Ready
- ✅ Database: MongoDB configured
- ⏳ Installation: Follow steps above
- 🚀 Next: Run SETUP.bat

---

**Let's get voting! 🗳️**
