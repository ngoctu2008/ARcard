import unittest
import os
from docx import Document
from docx.enum.section import WD_ORIENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.shared import Pt
from office_lib import OfficeAutomator

class TestOfficeApp(unittest.TestCase):
    def setUp(self):
        # Create a fresh sample docx
        self.doc = Document()
        self.doc.add_paragraph("Test Paragraph")
        self.doc.save("test_verify.docx")
        self.automator = OfficeAutomator()

    def tearDown(self):
        if os.path.exists("test_verify.docx"):
            os.remove("test_verify.docx")

    def test_orientation(self):
        self.automator.load_document("test_verify.docx")
        self.automator.set_orientation_whole_doc("LANDSCAPE")
        self.automator.save_document()

        doc = Document("test_verify.docx")
        section = doc.sections[0]
        self.assertEqual(section.orientation, WD_ORIENT.LANDSCAPE)

    def test_format_text(self):
        self.automator.load_document("test_verify.docx")
        self.automator.set_font_style(font_name="Arial", font_size=20)
        self.automator.set_paragraph_style(line_spacing=2.0)
        self.automator.save_document()

        doc = Document("test_verify.docx")
        p = doc.paragraphs[0]
        # Note: setting font style iterates over existing runs.
        # If the paragraph was created blank in setUp, it might not have runs unless text is added.
        # setUp adds "Test Paragraph", which usually creates one run.
        if p.runs:
            run = p.runs[0]
            self.assertEqual(run.font.name, "Arial")
            self.assertEqual(run.font.size, Pt(20))
        self.assertEqual(p.paragraph_format.line_spacing, 2.0)

    def test_page_number(self):
        self.automator.load_document("test_verify.docx")
        self.automator.add_page_number("RIGHT")
        self.automator.save_document()

        doc = Document("test_verify.docx")
        section = doc.sections[0]
        footer = section.footer
        p = footer.paragraphs[0]
        self.assertEqual(p.alignment, WD_ALIGN_PARAGRAPH.RIGHT)
        # Check if PAGE field is present (basic XML check)
        xml = p._element.xml
        self.assertIn('PAGE', xml)

if __name__ == '__main__':
    unittest.main()
