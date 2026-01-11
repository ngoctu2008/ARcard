from playwright.sync_api import sync_playwright
import os

# Create a mock HTML file that simulates the rendered template
html_content = ""
with open("verification/template.tpl", "r") as f:
    html_content = f.read()

# Replace Template Variables with Mock Data
mock_replacements = {
    "{NV_BASE_SITEURL}": "./",
    "{NV_LANG_DATA}": "vi",
    "{MODULE_NAME}": "avatar",
    "{NV_NAME_VARIABLE}": "nv",
    "{NV_OP_VARIABLE}": "op",
    "{NV_LANG_VARIABLE}": "lang",
    "{LANG.step_upload_photo}": "1. Chọn ảnh",
    "{LANG.step_edit}": "2. Chỉnh sửa",
    "{LANG.step_preview}": "3. Xem trước",
    "{LANG.step_download}": "4. Tải về",
    "{LANG.upload_your_photo}": "Tải ảnh của bạn lên",
    "{LANG.drag_drop_support}": "Kéo thả hoặc nhấn vào đây",
    "{LANG.click_to_upload}": "Tải ảnh lên",
    "{LANG.back}": "Quay lại",
    "{LANG.tab_image}": "Hình ảnh",
    "{LANG.tab_text}": "Văn bản",
    "{LANG.zoom}": "Thu phóng",
    "{LANG.rotate}": "Xoay",
    "{LANG.change_photo}": "Đổi ảnh",
    "{LANG.add_text_btn}": "Thêm chữ",
    "{LANG.select_text_to_edit}": "Chọn văn bản để chỉnh sửa",
    "{LANG.tab_position}": "Vị trí",
    "{LANG.tab_font}": "Font",
    "{LANG.tab_format}": "Định dạng",
    "{LANG.tab_color}": "Màu sắc",
    "{LANG.tab_outline}": "Viền",
    "{LANG.size}": "Kích thước",
    "{LANG.text_color}": "Màu chữ",
    "{LANG.delete}": "Xóa",
    "{LANG.finish_preview}": "Hoàn tất",
    "{LANG.congratulations}": "Chúc mừng!",
    "{LANG.success_msg}": "Bạn đã tạo avatar thành công",
    "{LANG.continue_editing}": "Sửa lại",
    "{LANG.download_image}": "Tải về máy",
    "{LANG.share_with_friends}": "Chia sẻ với bạn bè",
    "{ROW.image}": "https://via.placeholder.com/800x800.png?text=Frame+Overlay", # Mock Frame
    "{ROW.id}": "1",
    "{ROW.title}": "Avatar Mẫu 1",
    "{ROW.views}": "123",
    "{ROW.downloads}": "456",
    "<!-- BEGIN: allow_use -->": "",
    "<!-- END: allow_use -->": "",
    "<!-- BEGIN: main -->": "",
    "<!-- END: main -->": ""
}

for key, val in mock_replacements.items():
    html_content = html_content.replace(key, val)

# Add Fabric JS dependency (CDN) because it is missing in the local files
html_content = html_content.replace(
    '<script src="./themes/default/js/avatar.js"></script>',
    '<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script><script src="./avatar.js"></script>'
)

# Ensure CSS is linked correctly
html_content = html_content.replace(
    '<link rel="stylesheet" href="./themes/default/css/avatar.css">',
    '<link rel="stylesheet" href="./avatar.css">'
)
# Add Font Awesome for icons
html_content = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">' + html_content

with open("verification/mock_detail.html", "w") as f:
    f.write(html_content)

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    page = browser.new_page()

    # Load the mock HTML
    page.goto("file://" + os.path.abspath("verification/mock_detail.html"))

    # 1. Capture Initial State (Step 2: Upload)
    page.screenshot(path="verification/step2_upload.png")

    # 2. Simulate File Upload to trigger Step 3
    # We need a dummy image file
    with open("verification/dummy_upload.jpg", "wb") as f:
        f.write(b'\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0c\x14\r\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a\x1f\x1e\x1d\x1a\x1c\x1c $.\' ",#\x1c\x1c(7),01444\x1f\'9=82<.342\xFF\xC0\x00\x0b\x08\x00d\x00d\x01\x01\x11\x00\xFF\xC4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b\xFF\xDA\x00\x08\x01\x01\x00\x00\x00?\x00\xfd\xfc\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\xa2\x8a(\x00') # Tiny dummy JPG

    page.set_input_files("#file-upload", "verification/dummy_upload.jpg")

    # Wait for canvas to init
    page.wait_for_timeout(2000)

    # 3. Capture Editor State (Step 3: Editor - Image Tab)
    page.screenshot(path="verification/step3_editor_image.png")

    # 4. Switch to Text Tab and Add Text
    page.click("text=Văn bản")
    page.wait_for_timeout(500)
    page.click("text=Thêm chữ")
    page.wait_for_timeout(500)
    page.screenshot(path="verification/step3_editor_text.png")

    # 5. Finish to Preview
    page.click("text=Hoàn tất")
    page.wait_for_timeout(2000) # Wait for generation
    page.screenshot(path="verification/step4_preview.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
