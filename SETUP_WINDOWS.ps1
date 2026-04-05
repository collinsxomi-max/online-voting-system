# MongoDB Voting System - Windows Setup Script
# This script installs Composer and MongoDB PHP driver for the voting system

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "MongoDB Voting System Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get the project directory
$projectDir = if ($PSScriptRoot) { $PSScriptRoot } else { Get-Location }
$phpPath = "C:\xampp\php\php.exe"
$xampPath = "C:\xampp"

Write-Host "Project Directory: $projectDir" -ForegroundColor Yellow
Write-Host ""

# Check XAMPP
Write-Host "Step 1: Checking XAMPP Installation..." -ForegroundColor Cyan
if (Test-Path $phpPath) {
    Write-Host "✓ XAMPP PHP found at $xampPath" -ForegroundColor Green
} else {
    Write-Host "✗ XAMPP not found at $xampPath" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please download and install XAMPP from: https://www.apachefriends.org/download.html" -ForegroundColor Yellow
    Write-Host "Then run this script again." -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""

# Check/Install Composer
Write-Host "Step 2: Setting up Composer..." -ForegroundColor Cyan
Push-Location $projectDir

if (Test-Path "composer.phar") {
    Write-Host "✓ Composer already installed" -ForegroundColor Green
} else {
    Write-Host "Downloading Composer installer..." -ForegroundColor Yellow
    
    try {
        & $phpPath -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        Write-Host "Installing Composer..." -ForegroundColor Yellow
        
        & $phpPath composer-setup.php
        
        if ($LASTEXITCODE -eq 0) {
            & $phpPath -r "unlink('composer-setup.php');"
            Write-Host "✓ Composer installed successfully" -ForegroundColor Green
        } else {
            Write-Host "✗ Composer installation failed" -ForegroundColor Red
            Write-Host "Try manually: $phpPath -r `"copy('https://getcomposer.org/installer', 'composer-setup.php');`"" -ForegroundColor Yellow
            Read-Host "Press Enter to continue"
        }
    } catch {
        Write-Host "✗ Error downloading Composer: $_" -ForegroundColor Red
        Write-Host "Check your internet connection and try again" -ForegroundColor Yellow
        Read-Host "Press Enter to exit"
        exit 1
    }
}

Write-Host ""

# Install MongoDB PHP Driver
if (Test-Path "vendor\autoload.php") {
    Write-Host "✓ MongoDB driver already installed" -ForegroundColor Green
} else {
    Write-Host "Step 3: Installing MongoDB PHP Driver..." -ForegroundColor Cyan
    Write-Host "This may take a few minutes..." -ForegroundColor Yellow
    
    try {
        & $phpPath composer.phar install --no-interaction --prefer-dist
        
        if (Test-Path "vendor\autoload.php") {
            Write-Host "✓ MongoDB driver installed successfully" -ForegroundColor Green
        } else {
            Write-Host "⚠ composer install completed but autoload.php not found" -ForegroundColor Yellow
            Write-Host "This might be a temporary issue. Check vendor directory:" -ForegroundColor Yellow
            Write-Host "If vendor directory is empty, try running:" -ForegroundColor Yellow
            Write-Host "  $phpPath composer.phar install --no-interaction" -ForegroundColor Cyan
        }
    } catch {
        Write-Host "✗ Error installing MongoDB driver: $_" -ForegroundColor Red
        Read-Host "Press Enter to continue"
    }
}

Write-Host ""

# Check MongoDB
Write-Host "Step 4: MongoDB Service Check..." -ForegroundColor Cyan

try {
    $result = & $phpPath -r @"
try {
    `$client = new MongoDB\Client('mongodb://localhost:27017');
    `$client->admin->command(['ping' => 1]);
    echo 'CONNECTED';
} catch (Exception `$e) {
    echo 'NOT_CONNECTED';
}
"@ 2>&1
    
    if ($result -contains "CONNECTED") {
        Write-Host "✓ MongoDB is running on localhost:27017" -ForegroundColor Green
    } else {
        Write-Host "⚠ MongoDB not running" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "To start MongoDB service:" -ForegroundColor Yellow
        Write-Host "  net start MongoDB" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Or download from: https://www.mongodb.com/try/download/community" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠ Could not test MongoDB connection" -ForegroundColor Yellow
    Write-Host "Make sure MongoDB PHP driver is installed" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✓ Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Your voting system is ready to use!" -ForegroundColor Green
Write-Host ""
Write-Host "Access the application:" -ForegroundColor Cyan
Write-Host "  http://localhost/Online_%20voting_system/" -ForegroundColor Yellow
Write-Host ""
Write-Host "Before you begin:" -ForegroundColor Cyan
Write-Host "  1. Make sure MongoDB is running" -ForegroundColor Yellow
Write-Host "     Command: net start MongoDB" -ForegroundColor Yellow
Write-Host ""
Write-Host "  2. Start XAMPP (Apache + MySQL)" -ForegroundColor Yellow
Write-Host ""
Write-Host "  3. Change the default admin password!" -ForegroundColor Red
Write-Host "     File: backend/admin_login.php" -ForegroundColor Yellow
Write-Host "     Default: admin / change-this-admin-password" -ForegroundColor Yellow
Write-Host ""

Pop-Location

Write-Host "For help, see:" -ForegroundColor Cyan
Write-Host "  - MONGODB_QUICKSTART.md" -ForegroundColor Yellow
Write-Host "  - MONGODB_SETUP.md" -ForegroundColor Yellow
Write-Host ""

Read-Host "Press Enter to exit"
