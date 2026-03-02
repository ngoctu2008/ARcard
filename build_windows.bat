@echo off
cd /d "%~dp0"
echo ===================================================
echo   OFFICE AUTOMATOR - BUILD SCRIPT
echo ===================================================

echo.
echo [1/4] Creating Virtual Environment...
if not exist "venv" (
    python -m venv venv
    if %errorlevel% neq 0 (
        echo [ERROR] Could not create virtual environment.
        pause
        exit /b %errorlevel%
    )
)

echo.
echo [2/4] Activating Virtual Environment...
call venv\Scripts\activate
if %errorlevel% neq 0 (
    echo [ERROR] Could not activate virtual environment.
    pause
    exit /b %errorlevel%
)

echo.
echo [3/4] Installing Dependencies...
python -m pip install --upgrade pip setuptools wheel
python -m pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo [ERROR] Error installing dependencies!
    echo.
    echo [TIP] If you see a "No such file or directory" error - Long Path - :
    echo        1. Move this project to a shorter path e.g. C:\OfficeApp.
    echo        2. Enable "Long Paths" in Windows Registry.
    pause
    exit /b %errorlevel%
)

echo.
echo [4/4] Building Executable...
python -m PyInstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
if %errorlevel% neq 0 (
    echo [ERROR] Error building executable!
    pause
    exit /b %errorlevel%
)

echo.
echo [SUCCESS] Build Complete!
echo.
echo The application is ready at: dist\OfficeAutomator.exe
echo.
dir "dist\OfficeAutomator.exe" | findstr "OfficeAutomator.exe"
echo.
echo You can now run 'setup.iss' to create the installer.

deactivate
pause
