# Building for Windows

## 1. Create Standalone Executable
First, compile the Python code into a single executable file.

1. **Install Python Dependencies**
   ```bash
   pip install -r requirements.txt
   ```

2. **Build with PyInstaller**
   ```bash
   pyinstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
   ```
   This creates `dist\OfficeAutomator.exe`.

## 2. Create Installer (Optional)
To create a professional installation wizard (`setup.exe`) that installs the program to `Program Files` and creates desktop shortcuts:

1. **Install Inno Setup**
   Download and install [Inno Setup](https://jrsoftware.org/isdl.php).

2. **Compile the Installer**
   - Open `setup.iss` with Inno Setup Compiler.
   - Click **Build > Compile**.

3. **Locate the Installer**
   The `OfficeAutomatorSetup.exe` will be created in the `Output` directory (or same directory if not specified).

   **You can send this `OfficeAutomatorSetup.exe` to users.**
