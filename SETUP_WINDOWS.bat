@echo off
REM MongoDB Setup Script for Windows/XAMPP

echo.
echo ========================================
echo MongoDB Setup for Voting System
echo ========================================
echo.

cd /d "%~dp0"

echo Step 1: Checking XAMPP PHP...
if exist "c:\xampp\php\php.exe" (
    echo ✓ XAMPP found at c:\xampp\php
) else (
    echo ✗ XAMPP PHP not found at c:\xampp\php
    echo Please install XAMPP first
    pause
    exit /b 1
)

echo.
echo Step 2: Downloading and Installing Composer...
if exist "composer.phar" (
    echo ✓ Composer already installed
) else (
    echo Downloading Composer...
    c:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    if errorlevel 1 (
        echo ✗ Failed to download Composer
        echo Please check your internet connection and try again
        pause
        exit /b 1
    )
    
    echo Installing Composer...
    c:\xampp\php\php.exe composer-setup.php
    if errorlevel 1 (
        echo ✗ Failed to install Composer
        pause
        exit /b 1
    )
    
    c:\xampp\php\php.exe -r "unlink('composer-setup.php');"
    echo ✓ Composer installed successfully
)

echo.
echo Step 3: Installing MongoDB PHP Driver...
echo This may take a few minutes...
c:\xampp\php\php.exe composer.phar install --no-interaction

if exist "vendor\autoload.php" (
    echo ✓ MongoDB PHP driver installed successfully
) else (
    echo ✗ Failed to install MongoDB driver
    echo Please try running manually:
    echo   c:\xampp\php\php.exe composer.phar install
    pause
    exit /b 1
)

echo.
echo Step 4: MongoDB Service...
echo.
echo MongoDB not found - would you like to install it?
echo(
echo Option 1: Download from https://www.mongodb.com/try/download/community
echo Option 2: I already have MongoDB running locally
echo(

set /p choice="Enter choice (1 or 2): "

if "%choice%"=="1" (
    start https://www.mongodb.com/try/download/community
    echo Please install MongoDB and then run this script again
    pause
    exit /b 0
)

if "%choice%"=="2" (
    echo Checking MongoDB connection on localhost:27017...
    c:\xampp\php\php.exe -r "^
    try {^
        $client = new MongoDB\Client('mongodb://localhost:27017');^
        $client->admin->command(['ping' => 1]);^
        echo 'MongoDB is running!';^
    } catch (Exception $e) {^
        echo 'MongoDB connection failed: ' . $e->getMessage();^
        exit(1);^
    }^
    "
    
    if errorlevel 1 (
        echo ✗ Could not connect to MongoDB
        echo Please ensure MongoDB is running on localhost:27017
        echo Start it with: net start MongoDB
        pause
        exit /b 1
    ) else (
        echo ✓ MongoDB is running
    )
)

echo.
echo ========================================
echo ✓ Setup Complete!
echo ========================================
echo.
echo Your voting system is ready to use:
echo   http://localhost/Online_voting_system/
echo.
echo Default admin credentials:
echo   Username: admin
echo   Password: change-this-admin-password
echo.
echo ⚠ Remember to change the admin password!
echo.
pause
