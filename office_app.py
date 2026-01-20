import tkinter as tk
from tkinter import ttk, filedialog, messagebox
from office_lib import OfficeAutomator
import os
import platform

class OfficeApp:
    def __init__(self, root):
        self.root = root
        self.root.title("TỰ ĐỘNG THIẾT LẬP CHO OFFICE")
        self.root.geometry("600x650") # Increased height for more controls

        self.automator = OfficeAutomator()

        self.setup_ui()

    def setup_ui(self):
        # Header
        header_frame = tk.Frame(self.root, bg="#007bff")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text=text("TỰ ĐỘNG THIẾT LẬP CHO OFFICE"),
                 bg="#007bff", fg="white", font=("Arial", 14, "bold"), pady=10).pack()
        tk.Label(header_frame, text=text("version 2.0"),
                 bg="#007bff", fg="white", font=("Arial", 8)).pack(side=tk.RIGHT, padx=5)

        # Main Scrollable Container (optional if window gets too tall, but 650 should fit)
        # For now, sticking to standard pack/grid.

        # --- Section 1: File Selection ---
        self.setup_file_section()

        # --- Section 2: Options ---
        self.setup_options_section()

        # --- Section 3: Font - Paragraph Setup ---
        self.setup_font_para_section()

        # --- Section 4: Summary (Visual) ---
        self.setup_summary_section()

        # --- Section 5: Advanced Options ---
        self.setup_advanced_section()

        # Footer Buttons
        footer_frame = tk.Frame(self.root, pady=10)
        footer_frame.pack(fill=tk.X, side=tk.BOTTOM)

        btn_apply = tk.Button(footer_frame, text="✔ Áp dụng", bg="aquamarine", width=20, command=self.apply_all_changes, font=("Arial", 10, "bold"))
        btn_apply.pack(side=tk.LEFT, padx=50)

        btn_exit = tk.Button(footer_frame, text="❌ Thoát", bg="lightpink", width=15, command=self.root.quit, font=("Arial", 10, "bold"))
        btn_exit.pack(side=tk.RIGHT, padx=50)

    def setup_file_section(self):
        file_frame = tk.LabelFrame(self.root, text="Áp dụng cho file Word có sẵn (tùy chọn)", padx=5, pady=5, fg="green", font=("Arial", 9, "bold"))
        file_frame.pack(fill=tk.X, padx=10, pady=2)

        f_top = tk.Frame(file_frame)
        f_top.pack(fill=tk.X)

        self.file_path_var = tk.StringVar()
        entry_file = tk.Entry(f_top, textvariable=self.file_path_var)
        entry_file.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 5))

        btn_browse = tk.Button(f_top, text="📁 Chọn file Word", bg="yellow", command=self.browse_file)
        btn_browse.pack(side=tk.LEFT)

        self.apply_all_var = tk.BooleanVar(value=True)
        tk.Checkbutton(file_frame, text="Áp dụng cho toàn bộ nội dung văn bản", variable=self.apply_all_var).pack(anchor="w")

    def setup_options_section(self):
        opt_frame = tk.Frame(self.root)
        opt_frame.pack(fill=tk.X, padx=10, pady=2)

        self.create_new_var = tk.BooleanVar(value=True)
        tk.Checkbutton(opt_frame, text="Luôn tạo tài liệu mới", variable=self.create_new_var).pack(side=tk.LEFT)

        self.show_word_var = tk.BooleanVar(value=True)
        tk.Checkbutton(opt_frame, text="Hiển thị Word sau khi áp dụng", variable=self.show_word_var).pack(side=tk.LEFT, padx=20)

    def setup_font_para_section(self):
        frame = tk.LabelFrame(self.root, text="Thiết lập Font - Đoạn văn", fg="green", font=("Arial", 9, "bold"), padx=5, pady=5)
        frame.pack(fill=tk.X, padx=10, pady=5)

        # Grid layout
        # Row 0: Font, Size, Ruler
        tk.Label(frame, text="Font:").grid(row=0, column=0, sticky="w")
        self.font_name_var = tk.StringVar(value="Times New Roman")
        ttk.Combobox(frame, textvariable=self.font_name_var, values=["Times New Roman", "Arial", "Calibri", "Verdana"], width=15).grid(row=0, column=1, sticky="w")

        tk.Label(frame, text="Cỡ:").grid(row=0, column=2, sticky="e")
        self.font_size_var = tk.StringVar(value="14")
        tk.Spinbox(frame, from_=8, to=72, textvariable=self.font_size_var, width=5).grid(row=0, column=3, sticky="w")

        self.ruler_var = tk.BooleanVar(value=True)
        tk.Checkbutton(frame, text="Mở Ruler", variable=self.ruler_var).grid(row=0, column=4, sticky="w", padx=10)

        # Row 1: Line Spacing, Spacing Before/After
        tk.Label(frame, text="Giãn dòng:").grid(row=1, column=0, sticky="w")
        self.line_spacing_var = tk.StringVar(value="1.15")
        ttk.Combobox(frame, textvariable=self.line_spacing_var, values=["1.0", "1.15", "1.5", "2.0"], width=5).grid(row=1, column=1, sticky="w")

        tk.Label(frame, text="Cách đoạn (pt):").grid(row=1, column=2, sticky="w")

        f_spacing = tk.Frame(frame)
        f_spacing.grid(row=1, column=3, columnspan=2, sticky="w")
        tk.Label(f_spacing, text="Trước:").pack(side=tk.LEFT)
        self.space_before_var = tk.StringVar(value="3")
        tk.Spinbox(f_spacing, from_=0, to=100, textvariable=self.space_before_var, width=4).pack(side=tk.LEFT, padx=2)

        tk.Label(f_spacing, text="Sau:").pack(side=tk.LEFT, padx=(5,0))
        self.space_after_var = tk.StringVar(value="3")
        tk.Spinbox(f_spacing, from_=0, to=100, textvariable=self.space_after_var, width=4).pack(side=tk.LEFT, padx=2)

        # Row 2: Indentation
        tk.Label(frame, text="Thụt lề đầu dòng:").grid(row=2, column=0, sticky="w")
        self.indent_type_var = tk.StringVar(value="(Không)")
        ttk.Combobox(frame, textvariable=self.indent_type_var, values=["(Không)", "First Line", "Hanging"], width=10, state="readonly").grid(row=2, column=1, sticky="w")

        tk.Label(frame, text="Giá trị:").grid(row=2, column=2, sticky="e")
        self.indent_val_var = tk.StringVar(value="1.27")
        f_indent = tk.Frame(frame)
        f_indent.grid(row=2, column=3, sticky="w")
        tk.Entry(f_indent, textvariable=self.indent_val_var, width=6).pack(side=tk.LEFT)
        tk.Label(f_indent, text="cm").pack(side=tk.LEFT)

        # Row 3: Paper Size
        tk.Label(frame, text="Khổ giấy:").grid(row=3, column=0, sticky="w")
        self.paper_size_var = tk.StringVar(value="A4")
        ttk.Combobox(frame, textvariable=self.paper_size_var, values=["A4", "Letter", "Legal", "A3", "A5"], width=10, state="readonly").grid(row=3, column=1, sticky="w")

        tk.Label(frame, text="(21 x 29.7 cm)", fg="gray").grid(row=3, column=2, columnspan=2, sticky="w")

    def setup_summary_section(self):
        frame = tk.LabelFrame(self.root, text="Tóm tắt tác vụ tự động", fg="#007bff", font=("Arial", 9, "bold"), padx=5, pady=5)
        frame.pack(fill=tk.X, padx=10, pady=5)

        lbl_text = """✓ Word: Khổ giấy/font/cỡ/giãn dòng/đoạn/thụt lề theo cài đặt
✓ Word: Tắt kiểm tra chính tả / grammar (Giả lập)
✓ Excel: Font mặc định theo cài đặt (Giả lập)
✓ Tắt Protected View / Start Screen (Giả lập)"""
        tk.Label(frame, text=lbl_text, justify=tk.LEFT, anchor="w", fg="#555").pack(fill=tk.X)

    def setup_advanced_section(self):
        frame = tk.LabelFrame(self.root, text="Tùy chọn nâng cao", fg="#d9534f", font=("Arial", 9, "bold"), padx=5, pady=5)
        frame.pack(fill=tk.X, padx=10, pady=5)

        # Checkboxes (Visual Only for now)
        f_checks = tk.Frame(frame)
        f_checks.pack(fill=tk.X)
        tk.Checkbutton(f_checks, text="Đặt làm mặc định Normal.dotm").pack(side=tk.LEFT)
        tk.Checkbutton(f_checks, text="Tắt Protected View").pack(side=tk.LEFT)
        tk.Checkbutton(f_checks, text="Bỏ Start Screen").pack(side=tk.LEFT)

        f_checks2 = tk.Frame(frame)
        f_checks2.pack(fill=tk.X)
        tk.Checkbutton(f_checks2, text="Tắt AutoCorrect / Tự động số thứ tự").pack(side=tk.LEFT)

        # Margins
        f_margins = tk.Frame(frame, pady=5)
        f_margins.pack(fill=tk.X)
        tk.Label(f_margins, text="Lề (cm):").pack(side=tk.LEFT)

        tk.Label(f_margins, text="Trên:").pack(side=tk.LEFT, padx=(5,2))
        self.margin_top_var = tk.StringVar(value="2")
        tk.Entry(f_margins, textvariable=self.margin_top_var, width=4).pack(side=tk.LEFT)

        tk.Label(f_margins, text="Dưới:").pack(side=tk.LEFT, padx=(5,2))
        self.margin_bottom_var = tk.StringVar(value="2")
        tk.Entry(f_margins, textvariable=self.margin_bottom_var, width=4).pack(side=tk.LEFT)

        tk.Label(f_margins, text="Trái:").pack(side=tk.LEFT, padx=(5,2))
        self.margin_left_var = tk.StringVar(value="3")
        tk.Entry(f_margins, textvariable=self.margin_left_var, width=4).pack(side=tk.LEFT)

        tk.Label(f_margins, text="Phải:").pack(side=tk.LEFT, padx=(5,2))
        self.margin_right_var = tk.StringVar(value="1.5")
        tk.Entry(f_margins, textvariable=self.margin_right_var, width=4).pack(side=tk.LEFT)

    def browse_file(self):
        filename = filedialog.askopenfilename(filetypes=[("Word Documents", "*.docx")])
        if filename:
            self.file_path_var.set(filename)
            try:
                self.automator.load_document(filename)
            except Exception as e:
                messagebox.showerror("Lỗi", str(e))

    def apply_all_changes(self):
        filepath = self.file_path_var.get()
        if not filepath:
            messagebox.showwarning("Cảnh báo", "Vui lòng chọn file Word!")
            return

        # Load if not loaded
        if not self.automator.doc or self.automator.doc_path != filepath:
            try:
                self.automator.load_document(filepath)
            except Exception as e:
                messagebox.showerror("Lỗi Load", str(e))
                return

        try:
            # 1. Font & Size
            self.automator.set_font_style(self.font_name_var.get(), self.font_size_var.get())

            # 2. Paragraph Spacing
            self.automator.set_paragraph_style(
                self.line_spacing_var.get(),
                self.space_before_var.get(),
                self.space_after_var.get()
            )

            # 3. Indentation
            indent_map = {"(Không)": "None", "First Line": "First Line", "Hanging": "Hanging"}
            self.automator.set_indentation(
                indent_map.get(self.indent_type_var.get(), "None"),
                self.indent_val_var.get()
            )

            # 4. Paper Size
            self.automator.set_paper_size(self.paper_size_var.get())

            # 5. Margins
            self.automator.set_margins(
                self.margin_top_var.get(),
                self.margin_bottom_var.get(),
                self.margin_left_var.get(),
                self.margin_right_var.get()
            )

            # Save
            target_path = filepath
            if self.create_new_var.get():
                base, ext = os.path.splitext(filepath)
                target_path = f"{base}_fixed{ext}"

            self.automator.save_document(target_path)

            msg = f"Đã xử lý xong!\nLưu tại: {target_path}"
            messagebox.showinfo("Thành công", msg)

            if self.show_word_var.get():
                self.open_file_if_windows(target_path)

        except Exception as e:
            messagebox.showerror("Lỗi Xử Lý", f"Đã xảy ra lỗi: {e}")

    def open_file_if_windows(self, filepath):
        if platform.system() == "Windows" and os.path.exists(filepath):
            try:
                os.startfile(filepath)
            except Exception:
                pass

def text(s):
    return s

if __name__ == "__main__":
    root = tk.Tk()
    app = OfficeApp(root)
    root.mainloop()
