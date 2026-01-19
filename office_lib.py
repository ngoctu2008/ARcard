import os
from docx import Document
from docx.shared import Inches, Pt
from docx.enum.section import WD_ORIENT

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

        new_width, new_height = None, None

        # We need to swap width and height depending on the target orientation
        # This is a simplification; handling all page sizes requires more logic
        # but usually setting the section orientation and swapping page dimensions is enough.

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

    def get_document_info(self):
        if self.doc is None:
            return "No document loaded"
        return f"Loaded: {self.doc_path}, Sections: {len(self.doc.sections)}"
