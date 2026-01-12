import os
from playwright.sync_api import sync_playwright, expect

def test_avatar_mock():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # Load the mock file
        file_path = os.path.abspath("verification/mock_avatar.html")
        page.goto(f"file://{file_path}")

        print("Page loaded")

        # 1. Click Simulate Upload
        page.get_by_role("button", name="Simulate Upload").click()

        print("Upload simulated")

        # 2. Wait for canvas to init
        expect(page.locator("#step-3")).to_be_visible()
        page.wait_for_timeout(1000)

        # 3. Verify Canvas exists (Use first() to avoid strict mode error if dupes exist)
        canvas_container = page.locator(".canvas-container").first
        expect(canvas_container).to_be_visible()

        # 4. Interact with the Canvas
        canvas = page.locator("canvas.lower-canvas").first
        if not canvas.is_visible():
             # Fallback if specific class not found
             canvas = page.locator("#c").first

        box = canvas.bounding_box()
        if not box:
             print("Canvas bounding box not found")
             exit(1)

        center_x = box["x"] + box["width"] / 2
        center_y = box["y"] + box["height"] / 2

        page.mouse.click(center_x, center_y)
        page.wait_for_timeout(500)

        # Take screenshot of selection
        screenshot_path = os.path.abspath("verification/avatar_selection.png")
        page.screenshot(path=screenshot_path)
        print(f"Screenshot saved to {screenshot_path}")

        # 5. Verify logic via JS evaluation
        active_type = page.evaluate("appState.canvas.getActiveObject() ? appState.canvas.getActiveObject().type : 'none'")
        print(f"Active Object Type: {active_type}")

        has_overlay = page.evaluate("!!appState.canvas.overlayImage")
        print(f"Has Overlay: {has_overlay}")

        if active_type == 'image' and has_overlay:
            print("SUCCESS: User image is selected underneath the overlay.")
        else:
            print("FAILURE: User image not selected or overlay missing.")
            exit(1)

        browser.close()

if __name__ == "__main__":
    test_avatar_mock()