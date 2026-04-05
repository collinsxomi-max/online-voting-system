#!/usr/bin/env powershell
# MongoDB Extension Installer for XAMPP Windows PHP 8.2

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "MongoDB PHP Extension Installer" -ForegroundColor Cyan
Write-Host "XAMPP Windows - PHP 8.2" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$PHP_PATH = "C:\xampp\php"
$PHP_EXE = "$PHP_PATH\php.exe"
$EXT_DIR = "$PHP_PATH\ext"
$PHP_INI = "$PHP_PATH\php.ini"

# Step 1: Check PHP
Write-Host "Step 1: Checking PHP installation..." -ForegroundColor Yellow
if (Test-Path $PHP_EXE) {
    Write-Host "✓ PHP found at $PHP_PATH" -ForegroundColor Green
} else {
    Write-Host "✗ PHP not found" -ForegroundColor Red
    exit 1
}

# Step 2: Get PHP version
Write-Host ""
Write-Host "Step 2: Checking PHP version..." -ForegroundColor Yellow
$version = & $PHP_EXE -r "echo PHP_VERSION;"
Write-Host "✓ PHP Version: $version" -ForegroundColor Green

# Step 3: Download MongoDB extension
Write-Host ""
Write-Host "Step 3: Downloading MongoDB extension..." -ForegroundColor Yellow
Write-Host "This may take 1-2 minutes..." -ForegroundColor Yellow

$url = "https://pecl.php.net/get/mongodb-1.16.2-8.2-nts-vc17-x64.zip"
$output = "mongodb_ext.zip"

try {
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    $webClient = New-Object System.Net.WebClient
    Write-Host "Downloading from PECL: $url" -ForegroundColor Gray
    $webClient.DownloadFile($url, $output)
    Write-Host "✓ Download complete" -ForegroundColor Green
} catch {
    Write-Host "✗ Download failed: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manual download:" -ForegroundColor Yellow
    Write-Host "1. Go to: https://pecl.php.net/package/mongodb" -ForegroundColor Cyan
    Write-Host "2. Download: mongodb-1.16.2  Windows" -ForegroundColor Cyan
    Write-Host "3. Extract DLL to: $EXT_DIR" -ForegroundColor Cyan
    Read-Host "Press Enter to exit"
    exit 1
}

# Step 4: Extract
Write-Host ""
Write-Host "Step 4: Extracting archive..." -ForegroundColor Yellow
if (Test-Path $output) {
    Expand-Archive -Path $output -DestinationPath . -Force
    Write-Host "✓ Extracted" -ForegroundColor Green
} else {
    Write-Host "✗ Archive not found" -ForegroundColor Red
    exit 1
}

# Step 5: Find and copy DLL
Write-Host ""
Write-Host "Step 5: Installing extension..." -ForegroundColor Yellow
$dll_files = Get-ChildItem -Recurse -Filter "php_mongodb.dll" -ErrorAction SilentlyContinue
if ($dll_files) {
    foreach ($dll in $dll_files) {
        Write-Host "Found: $($dll.FullName)" -ForegroundColor Cyan
        Copy-Item -Path $dll.FullName -Destination "$EXT_DIR\php_mongodb.dll" -Force
        if (Test-Path "$EXT_DIR\php_mongodb.dll") {
            Write-Host "✓ Copied to $EXT_DIR" -ForegroundColor Green
            break
        }
    }
} else {
    Write-Host "✗ Could not find php_mongodb.dll" -ForegroundColor Red
    exit 1
}

# Step 6: Enable in php.ini
Write-Host ""
Write-Host "Step 6: Enabling in php.ini..." -ForegroundColor Yellow
$ini_content = Get-Content $PHP_INI
if ($ini_content -match "extension=.*mongodb") {
    Write-Host "✓ MongoDB already in php.ini" -ForegroundColor Green
} else {
    Add-Content -Path $PHP_INI -Value ""
    Add-Content -Path $PHP_INI -Value "; MongoDB Extension"
    Add-Content -Path $PHP_INI -Value "extension=php_mongodb.dll"
    Write-Host "✓ Added extension=php_mongodb.dll to php.ini" -ForegroundColor Green
}

# Step 7: Cleanup
Write-Host ""
Write-Host "Step 7: Cleaning up..." -ForegroundColor Yellow
if (Test-Path $output) {
    Remove-Item $output -Force
}
if (Test-Path "mongodb") {
    Remove-Item "mongodb" -Recurse -Force -ErrorAction SilentlyContinue
}
Write-Host "✓ Cleaned temporary files" -ForegroundColor Green

# Step 8: Verify
Write-Host ""
Write-Host "Step 8: Verifying installation..." -ForegroundColor Yellow
$modules = & $PHP_EXE -m
if ($modules -match "mongodb") {
    Write-Host "✓ MongoDB extension is LOADED!" -ForegroundColor Green
} else {
    Write-Host "⚠ Could not verify - you may need to restart Apache" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✓ Installation Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Restart Apache (XAMPP Control Panel)" -ForegroundColor Yellow
Write-Host "2. Install Composer packages:" -ForegroundColor Yellow
Write-Host "   cd C:\xampp\htdocs\Online_voting_system" -ForegroundColor Cyan
Write-Host "   C:\xampp\php\php.exe composer.phar install" -ForegroundColor Cyan
Write-Host "3. Start MongoDB:" -ForegroundColor Yellow
Write-Host "   net start MongoDB" -ForegroundColor Cyan
Write-Host "4. Open:" -ForegroundColor Yellow
Write-Host "   http://localhost/Online_voting_system/" -ForegroundColor Cyan
Write-Host ""

Read-Host "Press Enter to exit"
