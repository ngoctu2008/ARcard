@echo off
cd /d "%~dp0"
echo ===================================================
echo   OFFICE AUTOMATOR - BUILD SCRIPT
echo ===================================================

echo.
echo [1/3] Updating pip and setuptools...
python -m pip install --upgrade pip setuptools wheel
if %errorlevel% neq 0 (
    echo [WARNING] Could not update pip/setuptools. Continuing...
)

echo.
echo [1/3] Installing dependencies...
python -m pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo [ERROR] Error installing dependencies!
    echo.
    echo [TIP] If you see a "No such file or directory" error with a long path:
    echo        1. Try running this script as Administrator.
    echo        2. Enable "Long Paths" in Windows Registry (see BUILD_INSTRUCTIONS.md).
    pause
    exit /b %errorlevel%
)

echo.
echo [2/3] Building Executable...
echo This may take a while...
python -m PyInstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
if %errorlevel% neq 0 (
    echo [ERROR] Error building executable!
    pause
    exit /b %errorlevel%
)

echo.
echo [3/3] Verifying Output...
if exist "dist\OfficeAutomator.exe" (
    echo [SUCCESS] Build Complete!
    echo.
    echo The application is ready at: dist\OfficeAutomator.exe
    echo.
    dir "dist\OfficeAutomator.exe" | findstr "OfficeAutomator.exe"
    echo.
    echo You can now run 'setup.iss' to create the installer.
) else (
    echo [ERROR] File executable not found in dist folder!
    pause
    exit /b 1
)

echo.
pause
