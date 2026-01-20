# Instructions for Building Windows Executable & Installer
(Hướng dẫn đóng gói ứng dụng cho Windows)

## Prerequisites (Yêu cầu)
1.  **Python 3.x** installed and added to PATH.
2.  **Inno Setup Compiler** installed (for creating the installer).

## Step 1: Build the Executable (Bước 1: Tạo file chạy .exe)
**Important:** You must perform this step first!
(Quan trọng: Bạn phải làm bước này trước!)

1.  Open the folder containing the source code.
2.  Double-click `build_windows.bat`.
3.  Wait for the process to complete. It will install dependencies and use PyInstaller to create the executable.
4.  Verify that `dist\OfficeAutomator.exe` exists.

## Step 2: Create the Installer (Bước 2: Tạo bộ cài đặt)
1.  Open `setup.iss` with Inno Setup Compiler.
2.  Click the "Compile" button (or press F9).
3.  The installer `OfficeAutomatorSetup.exe` will be created in the `Output` folder (or project root depending on config).

## Troubleshooting (Khắc phục lỗi)
*   **"Source file ... does not exist"**: This means you skipped Step 1, or Step 1 failed. Run `build_windows.bat` and check for errors.
    (Lỗi này nghĩa là bạn chưa chạy Bước 1, hoặc Bước 1 bị lỗi. Hãy chạy lại `build_windows.bat`).
*   **"pip is not recognized"**: Ensure Python is installed and checked "Add to PATH" during installation.
*   **Antivirus Interference**: Sometimes Antivirus software (Windows Defender, etc.) may flag the new .exe as suspicious and delete/quarantine it. If the file disappears, check your Antivirus history.
    (Đôi khi phần mềm diệt virus có thể xóa nhầm file .exe vừa tạo. Nếu file bị mất, hãy kiểm tra lịch sử phần mềm diệt virus).
*   **"No such file or directory" (Long Path Error)**: If you see an error about a file path being too long during installation:
    1.  Press `Win + R`, type `regedit`, and press Enter.
    2.  Navigate to `HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\FileSystem`.
    3.  Find `LongPathsEnabled` and set it to `1`.
    4.  Restart your computer and try again.
    (Nếu gặp lỗi đường dẫn quá dài, hãy bật "LongPathsEnabled" trong Registry hoặc Google từ khóa "Enable Win32 Long Paths").
