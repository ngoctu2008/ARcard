import unittest
import os
from office_lib import OfficeAutomator
from docx import Document
from docx.shared import Pt, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH

class TestOfficeAutomator(unittest.TestCase):
    def setUp(self):
        self.test_file = "test_doc_verify.docx"
        self.doc = Document()
        self.doc.add_paragraph("Hello   World  With  Spaces")
        self.doc.add_paragraph("Line 2")
        self.doc.add_paragraph("") # Empty
        self.doc.add_paragraph("   ") # Empty whitespace
        self.doc.save(self.test_file)

        self.automator = OfficeAutomator()
        self.automator.load_document(self.test_file)

    def tearDown(self):
        if os.path.exists(self.test_file):
            os.remove(self.test_file)
        if os.path.exists("test_doc_verify.pdf"):
            os.remove("test_doc_verify.pdf")

    def test_alignment(self):
        self.automator.set_alignment("CENTER")
        p = self.automator.doc.paragraphs[0]
        self.assertEqual(p.alignment, WD_ALIGN_PARAGRAPH.CENTER)

    def test_clean_spaces(self):
        self.automator.clean_extra_spaces()
        p = self.automator.doc.paragraphs[0]
        self.assertEqual(p.text, "Hello World With Spaces")

    def test_clean_empty_lines(self):
        # Initial count: 4 paragraphs
        self.assertEqual(len(self.automator.doc.paragraphs), 4)
        self.automator.clean_empty_lines()
        # Should remove index 2 ("") and 3 ("   ")
        # Remaining: "Hello..." and "Line 2"
        self.assertEqual(len(self.automator.doc.paragraphs), 2)
        self.assertEqual(self.automator.doc.paragraphs[1].text, "Line 2")

    def test_page_number_logic(self):
        # Just test api call doesn't crash, logic verification requires xml inspection
        self.assertTrue(self.automator.add_page_number(start_at=5, skip_first=True))
        sectPr = self.automator.doc.sections[0]._sectPr
        # Check if titlePg is set (different first page)
        # It's mapped to sectPr.titlePg in python-docx usually?
        # Actually it's property on section
        self.assertTrue(self.automator.doc.sections[0].different_first_page_header_footer)

if __name__ == '__main__':
    unittest.main()
