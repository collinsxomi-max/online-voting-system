# DIRECT INSTALLATION - Manual Setup (5 Minutes)

## ⚡ Fastest Way to Get Your Voting System Working NOW

---

## 🎯 Option 1: Use Composer (Automatically Installs Everything)

This is the EASIEST option. Just follow these steps:

### Step 1: Open Windows Command Prompt

1. **Press: `Windows Key + R`**
2. **Type:** `cmd`
3. **Press: Enter**

A black window opens.

### Step 2: Install MongoDB Driver

**Paste this command and press Enter:**

```cmd
cd c:\xampp\htdocs\Online_voting_system && c:\xampp\php\php.exe composer.phar install
```

This will:
- Automatically download MongoDB PHP library
- Set up all dependencies
- Create a `vendor` folder with everything needed

**⏯️ Relax - this takes 10-15 minutes**
- Grab coffee/water
- It's downloading and installing packages
- Don't interrupt it!

### Step 3: Start MongoDB Service

**In the same Command Prompt window, paste:**

```cmd
net start MongoDB
```

If MongoDB hasn't been installed yet, install it from:
https://www.mongodb.com/try/download/community

(Or you already have it running)

### Step 4: Start XAMPP

1. Open XAMPP Control Panel
2. Click **Start** next to Apache
3. (MySQL is optional)

### Step 5: Open Your Voting System

Open your browser and go to:
```
http://localhost/Online_voting_system/
```

**✅ DONE!** You should see the voting system homepage!

---

## 🎯 Option 2: Install MongoDB PECL Extension (If Composer Fails)

This installs the MongoDB driver directly into PHP:

### Step 1: Download Extension from Windows PHP website

1. Go to: https://windows.php.net/downloads/pecl/
2. Find: **mongodb** →Latest Release
3. Download the file for your PHP version
   - Right-click → Copy Link
   - Paste in browser

For PHP 8.2 (XAMPP 8.2.12), look for file containing:
```
php_mongodb-*-8.2-nts-vc17-x64.zip
```

### Step 2: Extract and Install

1. **Right-click the ZIP file** → Extract All
2. **Look for:** `php_mongodb.dll`
3. **Copy it to:**
   ```
   C:\xampp\php\ext\php_mongodb.dll
   ```

### Step 3: Enable in PHP

1. **Open:** `C:\xampp\php\php.ini`
2. **Find the line:**
   ```
   ;extension=mongodb
   ```
   (It has a semicolon at the start)

3. **Change to:**
   ```
   extension=mongodb
   ```
   (Remove the semicolon)

4. **Save the file** (Ctrl+S)

5. **Restart Apache** (XAMPP Control Panel → Stop Apache → Start Apache)

### Step 4: Run Composer Again

```cmd
cd c:\xampp\htdocs\Online_voting_system
c:\xampp\php\php.exe composer.phar install
```

Now it will work!

### Step 5: Continue from Option 1 Steps 3-5

---

## ✅ Testing It Works

After running either option, test the installation:

### Test 1: Check in Command Prompt

```cmd
c:\xampp\php\php.exe -m | findstr mongodb
```

Should show: `mongodb`

### Test 2: Access the Website

Open browser:
```
http://localhost/Online_voting_system/
```

Should see: The voting system homepage with no errors!

---

## 🆘 If Something Goes Wrong

### Problem: "Command not found"
**Solution:** Make sure you're in the right directory:
```cmd
cd c:\xampp\htdocs\Online_voting_system
```

### Problem: "PHP not found"
**Solution:** XAMPP not installed. Download from:
https://www.apachefriends.org/download.html

### Problem: "MongoDB connection failed"
**Solution:** Start MongoDB:
```cmd
net start MongoDB
```

### Problem: Composer taking too long
**Solution:** It's normal! This can take 10-15 minutes. Let it finish.
- Check every few minutes if `vendor\autoload.php` exists
- If it appears, the installation is complete!

---

## 🎯 Next Steps (After Installation)

1. **Register Student Account**
   - Go to: `/frontend/register.php`
   - Create an account

2. **Admin Setup**
   - Go to: `/frontend/admin_login.php`
   - Use: username: `admin` password: `change-this-admin-password`
   - Create election positions
   - Add candidates

3. **CHANGE ADMIN PASSWORD!**
   - Edit: `backend/admin_login.php`
   - Find lines 7-8
   - Change the credentials

4. **Students Vote**
   - Login as student
   - Cast votes
   - View live results

---

##  Complete!

You now have a fully functional voting system powered by MongoDB! 🎉

**Any issues?** Check the documentation files:
- `MONGODB_QUICKSTART.md`
- `MONGODB_SETUP.md`
- `INSTALLATION_METHODS.md`

**Let me know if you get stuck!** 💪
