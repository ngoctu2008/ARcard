import os
from docx import Document
from docx.shared import Pt

TEMPLATE_DIR = 'templates'
OUTPUT_DIR = 'output'

def get_templates():
    """Returns a list of .docx files in the templates directory."""
    if not os.path.exists(TEMPLATE_DIR):
        return []
    return [f for f in os.listdir(TEMPLATE_DIR) if f.endswith('.docx')]

def process_templates(data):
    """
    Reads all templates, replaces placeholders with data, and saves to output.

    Args:
        data (dict): A dictionary mapping placeholder keys (e.g., 'ADDRESS')
                     to replacement values.

    Returns:
        list: A list of paths to the generated files.
    """
    if not os.path.exists(OUTPUT_DIR):
        os.makedirs(OUTPUT_DIR)

    generated_files = []

    templates = get_templates()
    for temp_name in templates:
        src_path = os.path.join(TEMPLATE_DIR, temp_name)
        dst_path = os.path.join(OUTPUT_DIR, temp_name)

        try:
            doc = Document(src_path)

            # Replace in Paragraphs
            for paragraph in doc.paragraphs:
                replace_text_in_paragraph(paragraph, data)

            # Replace in Tables
            for table in doc.tables:
                for row in table.rows:
                    for cell in row.cells:
                        for paragraph in cell.paragraphs:
                             replace_text_in_paragraph(paragraph, data)

            doc.save(dst_path)
            generated_files.append(dst_path)
            print(f"Processed: {dst_path}")

        except Exception as e:
            print(f"Error processing {src_path}: {e}")

    return generated_files

def replace_text_in_paragraph(paragraph, data):
    """
    Replaces placeholders in a paragraph.
    Note: Setting paragraph.text directly preserves paragraph style
    but resets character-level formatting (runs).
    """
    if not paragraph.text:
        return

    text = paragraph.text
    modified = False

    for key, value in data.items():
        placeholder = "{{" + key + "}}"
        if placeholder in text:
            text = text.replace(placeholder, value)
            modified = True

    if modified:
        paragraph.text = text
        # Enforce Times New Roman for the new text
        for run in paragraph.runs:
            run.font.name = 'Times New Roman'

def get_installed_documents():
    """Returns a list of files in the output directory."""
    if not os.path.exists(OUTPUT_DIR):
        return []
    return sorted([f for f in os.listdir(OUTPUT_DIR) if f.endswith('.docx')])
