from office_lib import OfficeAutomator
from docx.enum.section import WD_ORIENT

def test_orientation():
    automator = OfficeAutomator()
    automator.load_document("sample.docx")

    # Test Landscape
    automator.set_orientation_whole_doc("LANDSCAPE")
    automator.save_document("sample_landscape.docx")

    automator2 = OfficeAutomator()
    automator2.load_document("sample_landscape.docx")
    section = automator2.doc.sections[0]
    print(f"Landscape Test: Orient={section.orientation}, W={section.page_width}, H={section.page_height}")
    assert section.orientation == WD_ORIENT.LANDSCAPE
    assert section.page_width > section.page_height

    # Test Portrait
    automator.set_orientation_whole_doc("PORTRAIT")
    automator.save_document("sample_portrait.docx")

    automator3 = OfficeAutomator()
    automator3.load_document("sample_portrait.docx")
    section = automator3.doc.sections[0]
    print(f"Portrait Test: Orient={section.orientation}, W={section.page_width}, H={section.page_height}")
    assert section.orientation == WD_ORIENT.PORTRAIT
    assert section.page_height > section.page_width

    print("Tests passed!")

if __name__ == "__main__":
    test_orientation()
