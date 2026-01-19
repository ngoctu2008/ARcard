# Building for Windows
To create a standalone executable (.exe) for Windows users, follow these steps:

1. **Install Dependencies**
   Ensure Python 3 is installed, then run:
   ```bash
   pip install -r requirements.txt
   ```

2. **Build the Executable**
   Run the following command to bundle the application into a single file:
   ```bash
   pyinstaller --noconsole --onefile --name "OfficeAutomator" office_app.py
   ```

3. **Locate the Output**
   The `OfficeAutomator.exe` file will be located in the `dist/` directory.
   You can distribute this file to Windows users; they do not need Python installed to run it.

# Running the Source
You can also run the script directly:
```bash
python office_app.py
```
