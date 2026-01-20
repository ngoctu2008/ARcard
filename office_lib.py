import os
from docx import Document
from docx.shared import Pt, Cm, Mm
from docx.enum.section import WD_ORIENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

class OfficeAutomator:
    def __init__(self):
        self.doc = None
        self.doc_path = None

    def load_document(self, path):
        """Loads a .docx document from the given path."""
        if not os.path.exists(path):
            raise FileNotFoundError(f"File not found: {path}")

        try:
            self.doc = Document(path)
            self.doc_path = path
            return True
        except Exception as e:
            raise Exception(f"Error loading document: {e}")

    def save_document(self, path=None):
        """Saves the document to the given path or overwrites the original."""
        if self.doc is None:
            raise Exception("No document loaded.")

        target_path = path if path else self.doc_path
        try:
            self.doc.save(target_path)
            return True
        except Exception as e:
            raise Exception(f"Error saving document: {e}")

    def set_orientation_whole_doc(self, orientation):
        """
        Sets the orientation for the whole document.
        orientation: 'PORTRAIT' or 'LANDSCAPE'
        """
        if self.doc is None:
            raise Exception("No document loaded.")

        for section in self.doc.sections:
            current_orient = section.orientation
            current_width = section.page_width
            current_height = section.page_height

            if orientation == 'LANDSCAPE':
                if current_orient == WD_ORIENT.PORTRAIT or current_orient is None:
                    section.orientation = WD_ORIENT.LANDSCAPE
                    section.page_width = current_height
                    section.page_height = current_width
            elif orientation == 'PORTRAIT':
                if current_orient == WD_ORIENT.LANDSCAPE:
                    section.orientation = WD_ORIENT.PORTRAIT
                    section.page_width = current_height
                    section.page_height = current_width

        return True

    def set_font_style(self, font_name="Times New Roman", font_size=14):
        if self.doc is None: raise Exception("No document loaded.")

        # Helper to set font
        def apply_font(run):
            if font_name:
                run.font.name = font_name
                # Force xml for East Asia fonts compatibility
                rPr = run._element.get_or_add_rPr()
                rFonts = rPr.get_or_add_rFonts()
                rFonts.set(qn('w:eastAsia'), font_name)
            if font_size:
                try:
                    run.font.size = Pt(float(font_size))
                except ValueError:
                    pass

        # Paragraphs
        for paragraph in self.doc.paragraphs:
            for run in paragraph.runs:
                apply_font(run)

        # Tables
        for table in self.doc.tables:
            for row in table.rows:
                for cell in row.cells:
                    for paragraph in cell.paragraphs:
                        for run in paragraph.runs:
                            apply_font(run)
        return True

    def set_paragraph_style(self, line_spacing=1.5, space_before=0, space_after=6):
        if self.doc is None: raise Exception("No document loaded.")

        def apply_para(p):
            if line_spacing:
                try:
                    p.paragraph_format.line_spacing = float(line_spacing)
                except ValueError:
                    pass
            if space_before is not None:
                try:
                    p.paragraph_format.space_before = Pt(float(space_before))
                except ValueError:
                    pass
            if space_after is not None:
                try:
                    p.paragraph_format.space_after = Pt(float(space_after))
                except ValueError:
                    pass

        for paragraph in self.doc.paragraphs:
            apply_para(paragraph)

        for table in self.doc.tables:
            for row in table.rows:
                for cell in row.cells:
                    for paragraph in cell.paragraphs:
                        apply_para(paragraph)
        return True

    def set_indentation(self, indent_type, indent_value_cm):
        """
        Sets indentation for all paragraphs.
        indent_type: 'None', 'First Line', 'Hanging'
        indent_value_cm: float (value in cm)
        """
        if self.doc is None: raise Exception("No document loaded.")

        val = Cm(float(indent_value_cm)) if indent_value_cm is not None else Cm(0)

        def apply_indent(p):
            if indent_type == 'None' or indent_type == '(Không)':
                p.paragraph_format.first_line_indent = 0
                p.paragraph_format.left_indent = 0
            elif indent_type == 'First Line':
                p.paragraph_format.first_line_indent = val
                p.paragraph_format.left_indent = 0
            elif indent_type == 'Hanging':
                p.paragraph_format.first_line_indent = -val
                p.paragraph_format.left_indent = val

        for paragraph in self.doc.paragraphs:
            apply_indent(paragraph)

        for table in self.doc.tables:
            for row in table.rows:
                for cell in row.cells:
                    for paragraph in cell.paragraphs:
                        apply_indent(paragraph)
        return True

    def set_paper_size(self, size_name):
        """
        Sets paper size for all sections.
        size_name: 'A4', 'Letter', etc.
        """
        if self.doc is None: raise Exception("No document loaded.")

        # Sizes in mm
        sizes = {
            'A4': (210, 297),
            'Letter': (215.9, 279.4),
            'Legal': (215.9, 355.6),
            'A3': (297, 420),
            'A5': (148, 210)
        }

        # Handle case variations
        key = None
        for k in sizes:
            if k.lower() == size_name.lower():
                key = k
                break

        if not key:
            return False # Size not found

        width_mm, height_mm = sizes[key]

        for section in self.doc.sections:
            # Respect orientation
            if section.orientation == WD_ORIENT.LANDSCAPE:
                section.page_width = Mm(height_mm)
                section.page_height = Mm(width_mm)
            else:
                section.page_width = Mm(width_mm)
                section.page_height = Mm(height_mm)

        return True

    def set_margins(self, top, bottom, left, right):
        """
        Sets margins for all sections in cm.
        """
        if self.doc is None: raise Exception("No document loaded.")

        for section in self.doc.sections:
            if top: section.top_margin = Cm(float(top))
            if bottom: section.bottom_margin = Cm(float(bottom))
            if left: section.left_margin = Cm(float(left))
            if right: section.right_margin = Cm(float(right))

        return True

    def add_page_number(self, align='CENTER'):
        # align: 'LEFT', 'CENTER', 'RIGHT'
        if self.doc is None: raise Exception("No document loaded.")

        alignment_map = {
            'LEFT': WD_ALIGN_PARAGRAPH.LEFT,
            'CENTER': WD_ALIGN_PARAGRAPH.CENTER,
            'RIGHT': WD_ALIGN_PARAGRAPH.RIGHT
        }
        wd_align = alignment_map.get(str(align).upper(), WD_ALIGN_PARAGRAPH.CENTER)

        for section in self.doc.sections:
            footer = section.footer
            # Access the first paragraph of the footer or create one
            if not footer.paragraphs:
                p = footer.add_paragraph()
            else:
                p = footer.paragraphs[0]

            p.alignment = wd_align
            p.clear() # Clear existing content

            run = p.add_run()

            # Add field code for PAGE
            fldChar1 = OxmlElement('w:fldChar')
            fldChar1.set(qn('w:fldCharType'), 'begin')

            instrText = OxmlElement('w:instrText')
            instrText.set(qn('xml:space'), 'preserve')
            instrText.text = "PAGE"

            fldChar2 = OxmlElement('w:fldChar')
            fldChar2.set(qn('w:fldCharType'), 'end')

            run._r.append(fldChar1)
            run._r.append(instrText)
            run._r.append(fldChar2)

        return True

    def get_document_info(self):
        if self.doc is None:
            return "No document loaded"
        return f"Loaded: {self.doc_path}, Sections: {len(self.doc.sections)}"
