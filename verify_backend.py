import unittest
import os
from docx import Document
from docx.enum.section import WD_ORIENT
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

    def test_landscape_conversion(self):
        self.automator.load_document("test_verify.docx")
        self.automator.set_orientation_whole_doc("LANDSCAPE")
        self.automator.save_document()

        # Verify
        doc = Document("test_verify.docx")
        section = doc.sections[0]
        self.assertEqual(section.orientation, WD_ORIENT.LANDSCAPE)
        self.assertTrue(section.page_width > section.page_height)

    def test_portrait_conversion(self):
        # Set to landscape first
        self.automator.load_document("test_verify.docx")
        self.automator.set_orientation_whole_doc("LANDSCAPE")
        self.automator.save_document()

        # Convert back to Portrait
        self.automator.set_orientation_whole_doc("PORTRAIT")
        self.automator.save_document()

        # Verify
        doc = Document("test_verify.docx")
        section = doc.sections[0]
        self.assertEqual(section.orientation, WD_ORIENT.PORTRAIT)
        self.assertTrue(section.page_height > section.page_width)

if __name__ == '__main__':
    unittest.main()
