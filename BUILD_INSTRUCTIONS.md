# HƯỚNG DẪN ĐÓNG GÓI ỨNG DỤNG CHO WINDOWS
(Instructions for Building Windows Executable & Installer)

## Yêu cầu chuẩn bị (Prerequisites)
1.  **Python 3.x**: Phải được cài đặt và đã chọn "Add to PATH" trong quá trình cài đặt.
2.  **Inno Setup Compiler**: Cần cài đặt phần mềm này để tạo bộ cài đặt (setup).

---

## Bước 1: Tạo file chạy .exe (Build Executable)
**Quan trọng:** Bạn phải thực hiện bước này đầu tiên!

1.  Mở thư mục chứa mã nguồn (source code).
2.  Chạy file `build_windows.bat` (nhấn đúp chuột).
3.  Chờ quá trình chạy hoàn tất. Script sẽ tự động tạo môi trường ảo (venv), cài đặt thư viện cần thiết và dùng PyInstaller để đóng gói ứng dụng.
4.  Sau khi xong, kiểm tra xem file `dist\OfficeAutomator.exe` có xuất hiện không.

---

## Bước 2: Tạo bộ cài đặt (Create Installer)
1.  Mở file `setup.iss` bằng phần mềm **Inno Setup Compiler**.
2.  Nhấn nút **Compile** trên thanh công cụ (hoặc nhấn phím `F9`).
3.  Sau khi chạy xong, bộ cài đặt `OfficeAutomatorSetup.exe` sẽ được tạo ra (thường nằm trong thư mục `Output` hoặc cùng thư mục gốc).

---

## Khắc phục lỗi thường gặp (Troubleshooting)

### 1. Lỗi "Source file ... does not exist"
*   **Nguyên nhân**: Bạn chưa chạy Bước 1, hoặc Bước 1 bị lỗi nên chưa tạo được file `.exe`.
*   **Khắc phục**: Chạy lại file `build_windows.bat` và chú ý xem có báo lỗi (ERROR) gì không.

### 2. Lỗi "pip is not recognized"
*   **Nguyên nhân**: Python chưa được thêm vào biến môi trường PATH.
*   **Khắc phục**: Cài đặt lại Python và nhớ tích chọn ô **"Add Python to PATH"**.

### 3. Phần mềm diệt virus (Antivirus Interference)
*   **Hiện tượng**: File `.exe` vừa tạo bị biến mất hoặc không chạy được.
*   **Nguyên nhân**: Windows Defender hoặc phần mềm diệt virus nhận diện nhầm file mới là virus.
*   **Khắc phục**: Kiểm tra lịch sử quét virus và khôi phục (Restore) file, hoặc tắt tạm thời diệt virus khi build.

### 4. Lỗi đường dẫn quá dài (Long Path Error / "No such file or directory")
*   **Hiện tượng**: Báo lỗi không tìm thấy file với đường dẫn rất dài (thường liên quan đến `pkg_resources` hoặc `site-packages`) khi chạy `build_windows.bat`.
*   **Khắc phục**:
    *   **Cách 1 (Khuyên dùng)**: Di chuyển toàn bộ thư mục dự án ra vị trí ngắn hơn (Ví dụ: `C:\OfficeApp`) thay vì để sâu trong `Downloads` hay `Documents`.
    *   **Cách 2**: Bật tính năng "Long Paths" của Windows:
        1.  Nhấn tổ hợp phím `Windows + R`, gõ `regedit` và nhấn Enter.
        2.  Tìm đến: `HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\FileSystem`
        3.  Tìm khóa `LongPathsEnabled`, sửa giá trị thành `1`.
        4.  Khởi động lại máy.
