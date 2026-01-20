@echo off
cd /d "%~dp0"
chcp 65001 >nul
echo ===================================================
echo   OFFICE AUTOMATOR - BUILD SCRIPT
echo   (Script tạo file chạy Windows)
echo ===================================================

echo.
echo [1/3] Checking/Installing dependencies (Cài đặt thư viện)...
echo Updating pip and setuptools to minimize path issues...
python -m pip install --upgrade pip setuptools wheel
if %errorlevel% neq 0 (
    echo [WARNING] Could not update pip/setuptools. Continuing...
)

echo Installing requirements...
python -m pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo [ERROR] Lỗi cài đặt thư viện! Vui lòng kiểm tra Python và pip.
    echo [ERROR] Error installing dependencies!
    echo.
    echo [TIP] If you see a "No such file or directory" error with a long path:
    echo        1. Try running this script as Administrator.
    echo        2. Enable "Long Paths" in Windows Registry (see BUILD_INSTRUCTIONS.md).
    echo [MẸO] Nếu lỗi đường dẫn quá dài, hãy bật "Long Paths" trong Windows hoặc chạy Admin.
    pause
    exit /b %errorlevel%
)

echo.
echo [2/3] Building Executable with PyInstaller (Đang đóng gói phần mềm)...
echo This may take a while... (Vui lòng chờ...)
python -m PyInstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
if %errorlevel% neq 0 (
    echo [ERROR] Lỗi đóng gói phần mềm!
    echo [ERROR] Error building executable!
    pause
    exit /b %errorlevel%
)

echo.
echo [3/3] Verifying Output (Kiểm tra kết quả)...
if exist "dist\OfficeAutomator.exe" (
    echo [SUCCESS] Build Complete! (Thành công!)
    echo.
    echo The application is ready at: dist\OfficeAutomator.exe
    echo File đã được tạo tại thư mục 'dist':
    dir "dist\OfficeAutomator.exe" | findstr "OfficeAutomator.exe"
    echo.
    echo Bây giờ bạn có thể chạy file 'setup.iss' để tạo bộ cài đặt.
) else (
    echo [ERROR] File executable not found in dist folder!
    echo [ERROR] Không tìm thấy file .exe trong thư mục dist!
    pause
    exit /b 1
)

echo.
pause
