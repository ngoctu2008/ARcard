import tkinter as tk
from tkinter import ttk, filedialog, messagebox
from office_lib import OfficeAutomator
import os
import platform

class OfficeApp:
    def __init__(self, root):
        self.root = root
        self.root.title("TỰ ĐỘNG THIẾT LẬP CHO OFFICE")
        self.root.geometry("600x500")

        self.automator = OfficeAutomator()

        self.setup_ui()

    def setup_ui(self):
        # Header
        header_frame = tk.Frame(self.root, bg="#007bff")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text=text("TỰ ĐỘNG THIẾT LẬP CHO OFFICE"),
                 bg="#007bff", fg="white", font=("Arial", 14, "bold"), pady=10).pack()
        tk.Label(header_frame, text=text("version 1.0"),
                 bg="#007bff", fg="white", font=("Arial", 8)).pack(side=tk.RIGHT, padx=5)

        # File Selection
        file_frame = tk.LabelFrame(self.root, text="Áp dụng cho file Word có sẵn", padx=10, pady=10, fg="green")
        file_frame.pack(fill=tk.X, padx=10, pady=5)

        self.file_path_var = tk.StringVar()
        entry_file = tk.Entry(file_frame, textvariable=self.file_path_var, width=50)
        entry_file.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 5))

        btn_browse = tk.Button(file_frame, text="📂 Chọn file Word", bg="yellow", command=self.browse_file)
        btn_browse.pack(side=tk.LEFT)

        self.apply_all_var = tk.BooleanVar(value=True)
        tk.Checkbutton(file_frame, text="Áp dụng cho toàn bộ nội dung văn bản", variable=self.apply_all_var).pack(anchor="w", pady=(5,0))

        # Tabs
        self.notebook = ttk.Notebook(self.root)
        self.notebook.pack(fill=tk.BOTH, expand=True, padx=10, pady=5)

        self.tab_font = ttk.Frame(self.notebook)
        self.tab_page_num = ttk.Frame(self.notebook)
        self.tab_orient = ttk.Frame(self.notebook)

        self.notebook.add(self.tab_font, text="📄 Thiết lập Font/Đoạn văn")
        self.notebook.add(self.tab_page_num, text="🔢 Số trang")
        self.notebook.add(self.tab_orient, text="📐 Hướng giấy")

        self.notebook.select(self.tab_orient) # Default to Orientation tab

        self.setup_orientation_tab()

        # Footer
        footer_frame = tk.Frame(self.root, pady=10)
        footer_frame.pack(fill=tk.X)

        btn_apply = tk.Button(footer_frame, text="✔ Áp dụng", bg="lightblue", width=15, command=self.apply_changes)
        btn_apply.pack(side=tk.LEFT, padx=50) # Just a placeholder action for now

        btn_exit = tk.Button(footer_frame, text="❌ Thoát", bg="lightblue", width=15, command=self.root.quit)
        btn_exit.pack(side=tk.RIGHT, padx=50)

    def setup_orientation_tab(self):
        tab = self.tab_orient

        # Whole Doc Group
        group_whole = tk.LabelFrame(tab, text="Hướng giấy toàn bộ tài liệu", fg="#007bff", padx=10, pady=10)
        group_whole.pack(fill=tk.X, padx=5, pady=5)

        tk.Label(group_whole, text="Chọn hướng giấy:").pack(side=tk.LEFT)

        self.orient_var = tk.StringVar(value="PORTRAIT")
        cbo_orient = ttk.Combobox(group_whole, textvariable=self.orient_var, values=["Đứng (Portrait)", "Ngang (Landscape)"], state="readonly")
        cbo_orient.pack(side=tk.LEFT, padx=5)
        cbo_orient.current(0)

        tk.Label(group_whole, text="Giữ nguyên giá trị...", fg="gray", font=("Arial", 8, "italic")).pack(side=tk.LEFT, padx=5)

        # Specific Page Group (UI Only for now)
        group_specific = tk.LabelFrame(tab, text="Xoay một số trang theo ý", fg="#d9534f", padx=10, pady=10)
        group_specific.pack(fill=tk.X, padx=5, pady=5)

        tk.Label(group_specific, text="Hướng trang cần xoay:").grid(row=0, column=0, sticky="w")
        ttk.Combobox(group_specific, values=["Ngang (Landscape)", "Đứng (Portrait)"], state="readonly").grid(row=0, column=1, padx=5)

        tk.Label(group_specific, text="Nhập số trang cần xoay:").grid(row=1, column=0, sticky="w", pady=5)
        tk.Entry(group_specific).grid(row=1, column=1, padx=5, sticky="ew")
        tk.Label(group_specific, text="Ví dụ: 3 hoặc 3,5,7-10", fg="gray").grid(row=1, column=2, sticky="w")

        tk.Label(group_specific, text="⚠ Tự động tạo Section Break...", fg="#d9534f", font=("Arial", 8, "italic")).grid(row=2, column=0, columnspan=3, sticky="w")

        tk.Button(group_specific, text="📐 Xoay trang đã chọn", bg="orange", command=lambda: messagebox.showinfo("Thông báo", "Chức năng này chưa được cài đặt.")).grid(row=3, column=0, pady=5)

        # Action Buttons
        frame_actions = tk.Frame(tab, pady=10)
        frame_actions.pack(fill=tk.X)

        btn_change_all = tk.Button(frame_actions, text="🔄 Đổi hướng toàn bộ", bg="lightgreen", command=self.change_orientation_all)
        btn_change_all.pack(side=tk.LEFT, padx=20)

        btn_reset = tk.Button(frame_actions, text="↩ Reset về dọc + Xóa Section", bg="pink")
        btn_reset.pack(side=tk.LEFT)

        tk.Label(tab, text="⚠ Lưu ý: Xoay trang sẽ tạo Section Break...", fg="#d9534f", font=("Arial", 8, "italic")).pack(pady=5)

    def browse_file(self):
        filename = filedialog.askopenfilename(filetypes=[("Word Documents", "*.docx")])
        if filename:
            self.file_path_var.set(filename)
            try:
                self.automator.load_document(filename)
                messagebox.showinfo("Thông báo", "Đã tải file thành công!")
            except Exception as e:
                messagebox.showerror("Lỗi", str(e))

    def change_orientation_all(self):
        if not self.file_path_var.get():
            messagebox.showwarning("Cảnh báo", "Vui lòng chọn file trước!")
            return

        if not messagebox.askyesno("Xác nhận", "Hành động này sẽ thay đổi trực tiếp file gốc. Bạn có chắc chắn muốn tiếp tục?"):
            return

        # Determine selection
        selection = self.orient_var.get()
        target_orient = "LANDSCAPE" if "Landscape" in selection else "PORTRAIT"

        try:
            self.automator.set_orientation_whole_doc(target_orient)
            # Save to a new file to avoid overwriting immediately, or just overwrite as per user expectation?
            # The UI doesn't specify save location. Let's overwrite for now or save as new.
            # Usually these tools modify inplace or ask. Let's modify inplace but backup might be good.
            # For simplicity: overwrite.
            self.automator.save_document()
            messagebox.showinfo("Thành công", f"Đã đổi hướng toàn bộ sang {target_orient}")
            self.open_file_if_windows()
        except Exception as e:
            messagebox.showerror("Lỗi", str(e))

    def open_file_if_windows(self):
        filepath = self.file_path_var.get()
        if platform.system() == "Windows" and os.path.exists(filepath):
            try:
                os.startfile(filepath)
            except Exception:
                pass # Ignore if fails

    def apply_changes(self):
        # This button in the footer usually applies all pending changes.
        # Since we have immediate buttons inside tabs, this might be redundant or a 'Commit' button.
        # For now, I'll make it do the same as 'Change Orientation All' if that tab is active.
        current_tab = self.notebook.index(self.notebook.select())
        if current_tab == 2: # Orientation tab
            self.change_orientation_all()
        else:
            messagebox.showinfo("Thông báo", "Chức năng này chưa được cài đặt cho tab hiện tại.")

def text(s):
    # Helper to return text (placeholder for translation if needed)
    return s

if __name__ == "__main__":
    root = tk.Tk()
    app = OfficeApp(root)
    root.mainloop()
