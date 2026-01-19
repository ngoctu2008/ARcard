@echo off
chcp 65001 >nul
echo ===================================================
echo   OFFICE AUTOMATOR - BUILD SCRIPT
echo   (Script tạo file chạy Windows)
echo ===================================================

echo.
echo [1/3] Checking/Installing dependencies (Cài đặt thư viện)...
python -m pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo [ERROR] Lỗi cài đặt thư viện! Vui lòng kiểm tra Python và pip.
    echo [ERROR] Error installing dependencies!
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
    echo File đã được tạo tại thư mục 'dist'.
    echo Bây giờ bạn có thể chạy file 'setup.iss' để tạo bộ cài đặt.
) else (
    echo [ERROR] File executable not found in dist folder!
    echo [ERROR] Không tìm thấy file .exe trong thư mục dist!
    pause
    exit /b 1
)

echo.
pause
