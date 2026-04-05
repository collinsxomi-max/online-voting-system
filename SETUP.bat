@echo off
REM ================================================
REM MongoDB Voting System - ONE-CLICK INSTALLER
REM ================================================
REM
REM This script handles the complete setup:
REM 1. Checks PHP and Composer
REM 2. Installs MongoDB dependencies
REM 3. Provides status and next steps
REM

setlocal enabledelayedexpansion

echo.
echo ============================================
echo   MongoDB Voting System Installer
echo ============================================
echo.

REM Get the current directory (the project root)
set "PROJECT_ROOT=%~dp0"
cd /d "%PROJECT_ROOT%"

echo [1/5] Checking PHP Installation...
where php.exe >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP not found in PATH
    echo.
    echo Please ensure XAMPP is installed and PHP is in your PATH variable
    echo XAMPP download: https://www.apachefriends.org
    pause
    exit /b 1
)

echo ✅ PHP found

REM Get PHP version
for /f "tokens=*" %%i in ('php.exe -v') do set PHP_VERSION=%%i & goto :php_version_done
:php_version_done
echo    Version: %PHP_VERSION%

echo.
echo [2/5] Checking MongoDB Extension...
php.exe -m 2>nul | find "mongodb" >nul
if %errorlevel% neq 0 (
    echo ❌ MongoDB PHP extension not loaded
    echo.
    echo IMPORTANT: You need to install the MongoDB extension first!
    echo.
    echo Follow these steps:
    echo   1. Open: INSTALL_NOW.md in your project folder
    echo   2. Follow Option 2: Install MongoDB PECL Extension
    echo   3. Then run this installer again
    echo.
    pause
    exit /b 1
) else (
    echo ✅ MongoDB extension loaded
)

echo.
echo [3/5] Checking Composer...
if not exist "composer.phar" (
    echo ❌ Composer not found
    echo.
    echo Downloading Composer...
    
    REM Try to download using curl (modern Windows)
    curl -sS https://getcomposer.org/installer | php.exe 2>nul
    
    if not exist "composer.phar" (
        echo ❌ Composer download failed
        echo.
        echo Please download manually: https://getcomposer.org/download/
        echo Place composer.phar in: %PROJECT_ROOT%
        pause
        exit /b 1
    )
)
echo ✅ Composer found

echo.
echo [4/5] Installing MongoDB PHP Library...
echo.
echo This may take 10-15 minutes. Please wait...
echo (You can safely ignore any warnings about missing extensions for other features)
echo.

php.exe composer.phar install

if not exist "vendor\autoload.php" (
    echo.
    echo ❌ Composer installation failed
    echo.
    echo Try running manually:
    echo   php.exe composer.phar install
    pause
    exit /b 1
)

echo.
echo ✅ Dependencies installed successfully!

echo.
echo [5/5] Configuration Complete
echo.
echo ============================================
echo   ✅ SETUP COMPLETE!
echo ============================================
echo.
echo Next steps:
echo.
echo 1. Start MongoDB:
echo    - If using MongoDB locally: net start MongoDB
echo    - Or ensure MongoDB service is running
echo.
echo 2. Start Apache:
echo    - Open XAMPP Control Panel
echo    - Click "Start" next to Apache
echo.
echo 3. Open the voting system:
echo    - Go to: http://localhost/Online_voting_system/
echo.
echo 4. Verify installation:
echo    - Check: http://localhost/Online_voting_system/check_installation.php
echo.
echo DEFAULT ADMIN CREDENTIALS (CHANGE THESE!):
echo    Username: admin
echo    Password: change-this-admin-password
echo.
echo ============================================
echo.

pause
