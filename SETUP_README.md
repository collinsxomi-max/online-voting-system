# 🚀 Quick Start - MongoDB Voting System

## ⚠️ IMPORTANT: Setup Required First

Your voting system requires **MongoDB PHP driver** to be installed before it can run.

---

## 🎯 Quick Setup (3 Steps)

### Step 1: Run the Setup Script

**Option A: PowerShell (Recommended for Windows)**
```powershell
# Right-click on SETUP_WINDOWS.ps1 and choose "Run with PowerShell"
# OR open PowerShell and run:
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope CurrentUser
cd C:\xampp\htdocs\Online_voting_system
.\SETUP_WINDOWS.ps1
```

**Option B: Command Prompt (Batch)**
```cmd
cd C:\xampp\htdocs\Online_voting_system
SETUP_WINDOWS.bat
```

**Option C: Manual Install**
```
cd C:\xampp\htdocs\Online_voting_system
C:\xampp\php\php.exe composer.phar install
```

### Step 2: Start MongoDB

```cmd
# Start MongoDB service
net start MongoDB

# OR if not installed, download from:
# https://www.mongodb.com/try/download/community
```

### Step 3: Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache** (optional, if using XAMPP web server)
3. You can use any web server (XAMPP, Laragon, etc.)

---

## ✅ Verify Installation

Once setup is complete, access:
```
http://localhost/Online_voting_system/
```

You should see the voting system homepage!

---

## 🔧 What Each Setup Method Does

### PowerShell Script (SETUP_WINDOWS.ps1)
- ✓ Checks XAMPP installation
- ✓ Downloads and installs Composer
- ✓ Installs MongoDB PHP driver
- ✓ Tests MongoDB connection
- ✓ Shows helpful instructions

### Batch Script (SETUP_WINDOWS.bat)
- ✓ Similar to PowerShell script
- ✓ Works in Command Prompt

### Manual Installation
```bash
# 1. Download Composer
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# 2. Install Composer
C:\xampp\php\php.exe composer-setup.php

# 3. Install dependencies
C:\xampp\php\php.exe composer.phar install

# 4. Clean up
del composer-setup.php
```

---

## 🐛 Troubleshooting

### Error: "MongoDB setup required"
**Solution:** Run the setup script above ▲

### Error: "MongoDB connection failed"
**Solution:** Start MongoDB service:
```cmd
net start MongoDB
```

### Error: "PHP not found"
**Solution:** Install XAMPP first:
- Download: https://www.apachefriends.org/download.html
- Run installer

### Error: "Composer failed"
**Solution:** Check internet connection and try again:
```cmd
C:\xampp\php\php.exe composer.phar install
```

---

## 📚 Full Documentation

After setup, read these files:
- **`MONGODB_QUICKSTART.md`** - Complete feature overview
- **`MONGODB_SETUP.md`** - Detailed configuration
- **`MIGRATION_SUMMARY.md`** - Technical details

---

## 🔐 Security Reminder

⚠️ **Change the admin password!**

File: `backend/admin_login.php`
- Default username: `admin`
- Default password: `change-this-admin-password`

Change before production!

---

## 🎯 Testing the System

Once everything is set up:

1. **Register a student**
   - Go to: http://localhost/Online_voting_system/frontend/register.php
   - Fill in registration details

2. **Login as student**
   - Go to: http://localhost/Online_voting_system/frontend/login.php

3. **Admin panel**
   - Go to: http://localhost/Online_voting_system/frontend/admin_login.php
   - Create election positions
   - Add candidates

4. **Vote**
   - Students can now vote

5. **View results**
   - Go to: http://localhost/Online_voting_system/results.php

---

## 📞 Still Having Issues?

1. Check the setup scripts created in your project directory
2. Read `MONGODB_SETUP.md` for detailed troubleshooting
3. Verify MongoDB is installed: `mongosh`
4. Check XAMPP PHP is working: `C:\xampp\php\php.exe -v`

---

**Next step:** Run one of the setup scripts above! 🚀
