import unittest
import os
from office_lib import OfficeAutomator
from docx import Document
from docx.shared import Pt, Cm, Mm
from docx.enum.section import WD_ORIENT

class TestOfficeAutomator(unittest.TestCase):
    def setUp(self):
        self.test_file = "test_doc.docx"
        self.doc = Document()
        self.doc.add_paragraph("Hello World")
        self.doc.add_section()
        self.doc.save(self.test_file)

        self.automator = OfficeAutomator()
        self.automator.load_document(self.test_file)

    def tearDown(self):
        if os.path.exists(self.test_file):
            os.remove(self.test_file)

    def test_load_document(self):
        self.assertIsNotNone(self.automator.doc)

    def test_set_font_style(self):
        self.automator.set_font_style("Arial", 12)
        # Verify
        p = self.automator.doc.paragraphs[0]
        run = p.runs[0]
        self.assertEqual(run.font.name, "Arial")
        self.assertEqual(run.font.size, Pt(12))

    def test_set_paragraph_style(self):
        self.automator.set_paragraph_style(2.0, 10, 10)
        p = self.automator.doc.paragraphs[0]
        self.assertEqual(p.paragraph_format.line_spacing, 2.0)
        self.assertEqual(p.paragraph_format.space_before, Pt(10))
        self.assertEqual(p.paragraph_format.space_after, Pt(10))

    def test_orientation(self):
        self.automator.set_orientation_whole_doc("LANDSCAPE")
        section = self.automator.doc.sections[0]
        self.assertEqual(section.orientation, WD_ORIENT.LANDSCAPE)

        self.automator.set_orientation_whole_doc("PORTRAIT")
        section = self.automator.doc.sections[0]
        self.assertEqual(section.orientation, WD_ORIENT.PORTRAIT)

    def test_indentation_first_line(self):
        val = 1.27
        self.automator.set_indentation("First Line", val)
        p = self.automator.doc.paragraphs[0]
        self.assertAlmostEqual(p.paragraph_format.first_line_indent.cm, val, places=2)
        self.assertEqual(p.paragraph_format.left_indent, 0)

    def test_indentation_hanging(self):
        val = 1.27
        self.automator.set_indentation("Hanging", val)
        p = self.automator.doc.paragraphs[0]
        self.assertAlmostEqual(p.paragraph_format.first_line_indent.cm, -val, places=2)
        self.assertAlmostEqual(p.paragraph_format.left_indent.cm, val, places=2)

    def test_paper_size(self):
        self.automator.set_paper_size("A4")
        section = self.automator.doc.sections[0]
        # A4 is 210mm x 297mm
        self.assertAlmostEqual(section.page_width.mm, 210, places=1)
        self.assertAlmostEqual(section.page_height.mm, 297, places=1)

    def test_margins(self):
        self.automator.set_margins(2, 2, 3, 1.5)
        section = self.automator.doc.sections[0]
        self.assertAlmostEqual(section.top_margin.cm, 2, places=2)
        self.assertAlmostEqual(section.bottom_margin.cm, 2, places=2)
        self.assertAlmostEqual(section.left_margin.cm, 3, places=2)
        self.assertAlmostEqual(section.right_margin.cm, 1.5, places=2)

if __name__ == '__main__':
    unittest.main()
