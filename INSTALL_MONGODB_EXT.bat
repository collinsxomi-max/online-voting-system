@echo off
REM MongoDB PHP Extension Installer for XAMPP Windows
REM Automatically downloads and installs the MongoDB extension

echo.
echo ========================================
echo MongoDB PHP Extension Installer
echo XAMPP Windows
echo ========================================
echo.

REM Detect PHP version and path
set PHP_PATH=C:\xampp\php
set PHP_EXE=%PHP_PATH%\php.exe
set EXT_DIR=%PHP_PATH%\ext

echo Step 1: Checking PHP installation...
if exist "%PHP_EXE%" (
    echo ✓ PHP found at %PHP_PATH%
) else (
    echo ✗ PHP not found at %PHP_PATH%
    echo Please make sure XAMPP is installed at C:\xampp
    pause
    exit /b 1
)

echo.
echo Step 2: Checking PHP version...
for /f "tokens=*" %%i in ('%PHP_EXE% -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
echo ✓ PHP Version: %PHP_VERSION%

echo.
echo Step 3: Downloading MongoDB extension...
echo This may take a minute...

REM Download MongoDB PECL extension for PHP 8.2 (Non Thread-Safe)
REM For Windows 64-bit with PHP 8.2
powershell -Command "^
try {^
    $url = 'https://pecl.php.net/get/mongodb-1.16.2-8.2-nts-vc17-x64.zip';^
    $output = 'mongodb_ext.zip';^
    Write-Host 'Downloading from: ' $url;^
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12;^
    (New-Object System.Net.WebClient).DownloadFile($url, $output);^
    Write-Host 'Download complete!';^
} catch {^
    Write-Host 'Error downloading: ' $_._Message;^
    exit 1;^
}^
" || (
    echo ✗ Failed to download MongoDB extension
    echo.
    echo Alternative: Download manually from:
    echo https://pecl.php.net/package/mongodb
    echo Choose: mongodb-1.16.2 - Windows
    echo Then extract to: %EXT_DIR%
    pause
    exit /b 1
)

echo ✓ Download complete

echo.
echo Step 4: Extracting extension...
if exist "mongodb_ext.zip" (
    powershell -Command "Expand-Archive -Path mongodb_ext.zip -DestinationPath . -Force"
    echo ✓ Extracted
) else (
    echo ✗ Could not find mongodb_ext.zip
    pause
    exit /b 1
)

echo.
echo Step 5: Installing to PHP...
REM Find the DLL file
for /f "tokens=*" %%f in ('dir /s /b "php_mongodb.dll" 2^>nul') do (
    echo Found DLL: %%f
    echo Copying to %EXT_DIR%...
    copy "%%f" "%EXT_DIR%\php_mongodb.dll" /Y
    if exist "%EXT_DIR%\php_mongodb.dll" (
        echo ✓ Extension copied to %EXT_DIR%
    ) else (
        echo ✗ Failed to copy extension
        pause
        exit /b 1
    )
)

echo.
echo Step 6: Enabling in php.ini...
set PHP_INI=%PHP_PATH%\php.ini

REM Check if extension is already enabled
findstr /I "extension=mongodb" "%PHP_INI%" >nul
if errorlevel 1 (
    REM Not found, add it
    echo.>> "%PHP_INI%"
    echo ; MongoDB Extension >> "%PHP_INI%"
    echo extension=php_mongodb.dll >> "%PHP_INI%"
    echo ✓ Added extension=php_mongodb.dll to php.ini
) else (
    echo ✓ MongoDB extension already enabled in php.ini
)

echo.
echo Step 7: Cleaning up...
if exist "mongodb_ext.zip" del mongodb_ext.zip
if exist "mongodb" rmdir /s /q mongodb 2>nul
echo ✓ Cleaned temporary files

echo.
echo Step 8: Verifying installation...
echo.
%PHP_EXE% -m | findstr /I "mongodb"
if errorlevel 0 (
    echo ✓ MongoDB extension is loaded!
) else (
    echo ⚠ Could not verify - you may need to restart Apache
)

echo.
echo ========================================
echo ✓ Installation Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Restart Apache (XAMPP Control Panel)
echo 2. Go to: http://localhost/Online_voting_system/
echo 3. System should now work!
echo.
pause
