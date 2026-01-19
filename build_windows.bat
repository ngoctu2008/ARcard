@echo off
echo ===================================================
echo   OFFICE AUTOMATOR - BUILD SCRIPT
echo ===================================================

echo [1/3] Checking/Installing dependencies...
pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo Error installing dependencies!
    pause
    exit /b %errorlevel%
)

echo.
echo [2/3] Building Executable with PyInstaller...
pyinstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
if %errorlevel% neq 0 (
    echo Error building executable!
    pause
    exit /b %errorlevel%
)

echo.
echo [3/3] Build Complete!
echo.
echo The application is ready at: dist\OfficeAutomator.exe
echo You can copy this file anywhere and run it directly.
echo.
pause
