# MongoDB Setup - Two Methods

Your voting system needs the MongoDB PHP driver. Choose the method that works best for you:

---

## Method 1: Install MongoDB PECL Extension (Fastest - Recommended)

This method installs only the MongoDB driver without downloading many packages.

### Windows Steps:

1. **Open Command Prompt as Administrator**

2. **Install MongoDB extension:**
   ```cmd
   cd c:\xampp\php
   php.exe pecl install mongodb
   ```

3. **Enable in php.ini:**
   - Open: `c:\xampp\php\php.ini`
   - Find: `;extension=mongodb` (with semicolon)
   - Change to: `extension=mongodb` (remove semicolon)
   - Save file

4. **Restart XAMPP/Apache**

5. **Verify installation:**
   ```cmd
   c:\xampp\php\php.exe -m | findstr mongodb
   ```
   Should show: `mongodb`

**Advantages:**
- ✓ Faster installation (just driver, no PHP library)
- ✓ Smaller download
- ✓ Built directly into PHP

---

## Method 2: Use Composer (Full Setup)

This installs both the driver AND the MongoDB PHP library.

### Steps:

1. **Wait for existing Composer to finish:**
   ```cmd
   cd c:\xampp\htdocs\Online_voting_system
   dir vendor
   ```
   If vendor directory appears, installation is complete!

2. **If still installing, monitor progress:**
   - Check periodically if `vendor\autoload.php` exists
   - This may take 5-10 minutes

3. **If it fails, try again:**
   ```cmd
   c:\xampp\php\php.exe composer.phar install --no-interaction
   ```

**Advantages:**
- ✓ Complete PHP MongoDB library
- ✓ Better OOP interface
- ✓ Additional helper functions

---

## ✅ How to Know It's Working

After choosing a method, test your setup:

1. **Navigate to project:**
   ```cmd
   cd c:\xampp\htdocs\Online_voting_system
   ```

2. **Test MongoDB connection:**
   ```cmd
   c:\xampp\php\php.exe -r "^
   try {^
       $client = new MongoDB\Client('mongodb://localhost:27017');^
       $client->admin->command(['ping' => 1]);^
       echo 'SUCCESS: MongoDB is connected!';^
   } catch (Exception $e) {^
       echo 'FAILED: ' . $e->getMessage();^
   }^
   "
   ```

3. **Access application:**
   - http://localhost/Online_voting_system/
   - Should show homepage without errors!

---

## Troubleshooting

### "MongoDB\Client not found" error
→ Method 1 (PECL) or Method 2 (Composer) not completed

### "pecl install failed"
→ Your PHP environment may not support PECL
→ Try Method 2 (Composer) instead

### Composer still downloading
→ It's normal, may take 5-15 minutes
→ Leave it running, check `vendor\autoload.php` periodically
→ Or try Method 1 instead

---

## Linux/Mac Users

### Method 1 (PECL):
```bash
pecl install mongodb
```

Then edit `/etc/php.ini` or `~/.phprc`:
```ini
extension=mongodb.so
```

### Method 2 (Composer):
```bash
cd /path/to/Online_voting_system
composer install
```

---

## Still Having Issues?

1. **Check Internet Connection:**
   ```cmd
   ping google.com
   ```

2. **Verify XAMPP PHP:**
   ```cmd
   c:\xampp\php\php.exe -v
   ```
   Should show PHP version

3. **Check MongoDB service:**
   ```cmd
   net start MongoDB
   ```

4. **Review these documents:**
   - `SETUP_README.md` - Quick start guide
   - `MONGODB_SETUP.md` - Detailed setup
   - `SETUP_WINDOWS.ps1` - Automated setup script

---

## Next Steps After Installation

1. **Start MongoDB:**
   ```cmd
   net start MongoDB
   ```

2. **Start XAMPP/Web Server**

3. **Access application:**
   ```
   http://localhost/Online_voting_system/
   ```

4. **Change admin password!**
   - Edit: `backend/admin_login.php`
   - Current: `admin` / `change-this-admin-password`

---

**Choose Method 1 for fastest results! 🚀**
