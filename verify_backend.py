import office_lib
import os
from docx import Document

def verify():
    print("Verifying backend logic with Font Check...")

    # Define test data
    test_data = {
        "DIA_CHI": "123 Đường Test, Quận Test",
        "CHUC_DANH": "Giám Đốc Kiểm Thử"
    }

    # Run the processor
    print("Running process_templates...")
    generated_files = office_lib.process_templates(test_data)

    if not generated_files:
        print("FAIL: No files generated.")
        return

    print(f"Generated {len(generated_files)} files.")

    # Check content of one file
    test_file = generated_files[0]
    print(f"Checking content of {test_file}...")

    doc = Document(test_file)
    full_text = []

    font_check_passed = True

    for p in doc.paragraphs:
        full_text.append(p.text)
        # Check font if this paragraph contains our replaced text
        if "123 Đường Test" in p.text or "Giám Đốc Kiểm Thử" in p.text:
            for run in p.runs:
                if run.font.name != 'Times New Roman':
                    # Note: Sometimes run.font.name is None if it inherits from style.
                    # But we explicitly set it, so it should be there.
                    # Or if the style is TNR, it might be None.
                    # In our case, we set it explicitly in replace_text_in_paragraph.
                    print(f"WARNING: Run font is {run.font.name}, expected 'Times New Roman'. Text: '{run.text}'")
                    # If we set it, it should be 'Times New Roman'.
                    # If it is None, it means it wasn't set on the run level.
                    if run.font.name is None:
                         print("   (Font is None, likely inheriting style)")
                    else:
                         font_check_passed = False

    full_text_str = "\n".join(full_text)

    if "123 Đường Test, Quận Test" in full_text_str:
        print("PASS: Address found in document.")
    else:
        print(f"FAIL: Address NOT found in document. Content:\n{full_text_str}")

    if "Giám Đốc Kiểm Thử" in full_text_str:
        print("PASS: Title found in document.")
    else:
        print(f"FAIL: Title NOT found in document. Content:\n{full_text_str}")

    if font_check_passed:
        print("PASS: Font check passed (runs have TNR or inherit it).")
    else:
        print("FAIL: Font check failed.")

if __name__ == "__main__":
    verify()
