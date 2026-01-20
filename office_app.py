import tkinter as tk
from tkinter import ttk, filedialog, messagebox
from office_lib import OfficeAutomator
import os
import platform
import threading

class OfficeApp:
    def __init__(self, root):
        self.root = root
        self.root.title("TỰ ĐỘNG THIẾT LẬP CHO OFFICE")
        self.root.geometry("800x700")

        self.automator = OfficeAutomator()

        # State variables
        self.file_path_var = tk.StringVar()
        self.is_folder_var = tk.BooleanVar(value=False)
        self.scope_var = tk.StringVar(value="ALL") # ALL or SECTION

        self.create_new_var = tk.BooleanVar(value=True)
        self.open_after_var = tk.BooleanVar(value=True)

        self.setup_ui()

    def setup_ui(self):
        # Header
        header_frame = tk.Frame(self.root, bg="#007bff")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="TỰ ĐỘNG THIẾT LẬP CHO OFFICE",
                 bg="#007bff", fg="white", font=("Arial", 14, "bold"), pady=10).pack()
        tk.Label(header_frame, text="version 3.0",
                 bg="#007bff", fg="white", font=("Arial", 8)).pack(side=tk.RIGHT, padx=5)

        # Tabs
        self.notebook = ttk.Notebook(self.root)
        self.notebook.pack(fill=tk.BOTH, expand=True, padx=5, pady=5)

        self.tab_general = ttk.Frame(self.notebook)
        self.tab_text = ttk.Frame(self.notebook)
        self.tab_page = ttk.Frame(self.notebook)
        self.tab_pagenum = ttk.Frame(self.notebook)
        self.tab_clean = ttk.Frame(self.notebook)
        self.tab_export = ttk.Frame(self.notebook)

        self.notebook.add(self.tab_general, text="Cấu hình Chung")
        self.notebook.add(self.tab_text, text="Định dạng Văn bản")
        self.notebook.add(self.tab_page, text="Thiết lập Trang")
        self.notebook.add(self.tab_pagenum, text="Đánh số trang")
        self.notebook.add(self.tab_clean, text="Dọn dẹp & Tiện ích")
        self.notebook.add(self.tab_export, text="Mục lục & Xuất bản")

        self.setup_tab_general()
        self.setup_tab_text()
        self.setup_tab_page()
        self.setup_tab_pagenum()
        self.setup_tab_clean()
        self.setup_tab_export()

        # Footer Buttons
        footer_frame = tk.Frame(self.root, pady=10)
        footer_frame.pack(fill=tk.X, side=tk.BOTTOM)

        btn_apply = tk.Button(footer_frame, text="✔ Áp dụng Tất cả", bg="aquamarine", width=20, command=self.apply_all_changes, font=("Arial", 10, "bold"))
        btn_apply.pack(side=tk.LEFT, padx=50)

        btn_exit = tk.Button(footer_frame, text="❌ Thoát", bg="lightpink", width=15, command=self.root.quit, font=("Arial", 10, "bold"))
        btn_exit.pack(side=tk.RIGHT, padx=50)

    # --- TAB 1: GENERAL ---
    def setup_tab_general(self):
        tab = self.tab_general

        # Scope Group
        grp_scope = tk.LabelFrame(tab, text="Phạm vi áp dụng (Scope)", fg="blue", padx=10, pady=10)
        grp_scope.pack(fill=tk.X, padx=10, pady=5)

        f_file = tk.Frame(grp_scope)
        f_file.pack(fill=tk.X)

        entry_file = tk.Entry(f_file, textvariable=self.file_path_var)
        entry_file.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 5))

        btn_browse = tk.Button(f_file, text="📂 Chọn...", bg="yellow", command=self.browse_file)
        btn_browse.pack(side=tk.LEFT)

        f_opts = tk.Frame(grp_scope, pady=5)
        f_opts.pack(fill=tk.X)
        tk.Checkbutton(f_opts, text="Xử lý hàng loạt (Folder)", variable=self.is_folder_var, command=self.on_mode_change).pack(side=tk.LEFT)

        tk.Label(f_opts, text="| Phạm vi:").pack(side=tk.LEFT, padx=10)
        ttk.Radiobutton(f_opts, text="Toàn bộ văn bản", variable=self.scope_var, value="ALL").pack(side=tk.LEFT)
        ttk.Radiobutton(f_opts, text="Chỉ Section hiện tại (Simulated)", variable=self.scope_var, value="SECTION", state="disabled").pack(side=tk.LEFT)

        # View Options Group (Simulated)
        grp_view = tk.LabelFrame(tab, text="Tùy chọn hiển thị (Word Settings)", fg="green", padx=10, pady=10)
        grp_view.pack(fill=tk.X, padx=10, pady=5)

        tk.Checkbutton(grp_view, text="Tắt Protected View (Giả lập)").grid(row=0, column=0, sticky="w")
        tk.Checkbutton(grp_view, text="Tắt Start Screen (Giả lập)").grid(row=0, column=1, sticky="w")
        tk.Checkbutton(grp_view, text="Tắt Spell/Grammar Check (Giả lập)").grid(row=1, column=0, sticky="w")
        tk.Checkbutton(grp_view, text="Ẩn Ruler (Giả lập)").grid(row=1, column=1, sticky="w")

        # Post-process Group
        grp_post = tk.LabelFrame(tab, text="Hành động sau xử lý", fg="purple", padx=10, pady=10)
        grp_post.pack(fill=tk.X, padx=10, pady=5)

        tk.Checkbutton(grp_post, text="Lưu thành file mới (Backup file gốc)", variable=self.create_new_var).pack(anchor="w")
        tk.Checkbutton(grp_post, text="Mở file sau khi làm xong", variable=self.open_after_var).pack(anchor="w")

    def on_mode_change(self):
        # Clear path when switching modes to avoid confusion
        self.file_path_var.set("")

    def browse_file(self):
        if self.is_folder_var.get():
            path = filedialog.askdirectory()
        else:
            path = filedialog.askopenfilename(filetypes=[("Word Documents", "*.docx")])

        if path:
            self.file_path_var.set(path)

    # --- TAB 2: TEXT FORMATTING ---
    def setup_tab_text(self):
        tab = self.tab_text

        # Typography
        grp_font = tk.LabelFrame(tab, text="Font chữ (Typography)", fg="blue", padx=10, pady=10)
        grp_font.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_font, text="Font:").grid(row=0, column=0, sticky="w")
        self.font_name_var = tk.StringVar(value="Times New Roman")
        ttk.Combobox(grp_font, textvariable=self.font_name_var, values=["Times New Roman", "Arial", "Calibri"]).grid(row=0, column=1)

        tk.Label(grp_font, text="Size:").grid(row=0, column=2, sticky="e", padx=10)
        self.font_size_var = tk.StringVar(value="14")
        tk.Spinbox(grp_font, textvariable=self.font_size_var, from_=8, to=72, width=5).grid(row=0, column=3)

        self.vni_convert_var = tk.BooleanVar()
        tk.Checkbutton(grp_font, text="Chuyển mã VNI -> Unicode (Basic)", variable=self.vni_convert_var, fg="red").grid(row=1, column=0, columnspan=4, sticky="w", pady=5)

        # Paragraph
        grp_para = tk.LabelFrame(tab, text="Đoạn văn (Paragraph)", fg="blue", padx=10, pady=10)
        grp_para.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_para, text="Căn lề:").grid(row=0, column=0, sticky="w")
        self.align_var = tk.StringVar(value="JUSTIFY")
        ttk.Combobox(grp_para, textvariable=self.align_var, values=["LEFT", "CENTER", "RIGHT", "JUSTIFY"], state="readonly", width=10).grid(row=0, column=1, sticky="w")

        tk.Label(grp_para, text="Line Spacing:").grid(row=1, column=0, sticky="w", pady=5)
        self.line_spacing_var = tk.StringVar(value="1.5")
        ttk.Combobox(grp_para, textvariable=self.line_spacing_var, values=["1.0", "1.15", "1.5"], width=5).grid(row=1, column=1, sticky="w")

        tk.Label(grp_para, text="Space Before (pt):").grid(row=0, column=2, sticky="e", padx=10)
        self.space_before_var = tk.StringVar(value="3")
        tk.Entry(grp_para, textvariable=self.space_before_var, width=5).grid(row=0, column=3)

        tk.Label(grp_para, text="Space After (pt):").grid(row=1, column=2, sticky="e", padx=10)
        self.space_after_var = tk.StringVar(value="3")
        tk.Entry(grp_para, textvariable=self.space_after_var, width=5).grid(row=1, column=3)

        tk.Label(grp_para, text="Thụt dòng (First Line):").grid(row=2, column=0, sticky="w", pady=5)
        self.indent_val_var = tk.StringVar(value="1.27")
        f_ind = tk.Frame(grp_para)
        f_ind.grid(row=2, column=1, columnspan=3, sticky="w")
        tk.Entry(f_ind, textvariable=self.indent_val_var, width=5).pack(side=tk.LEFT)
        tk.Label(f_ind, text="cm").pack(side=tk.LEFT)

    # --- TAB 3: PAGE SETUP ---
    def setup_tab_page(self):
        tab = self.tab_page

        # Paper
        grp_paper = tk.LabelFrame(tab, text="Khổ giấy (Paper)", fg="blue", padx=10, pady=10)
        grp_paper.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_paper, text="Size:").pack(side=tk.LEFT)
        self.paper_size_var = tk.StringVar(value="A4")
        ttk.Combobox(grp_paper, textvariable=self.paper_size_var, values=["A4", "A3", "Letter"], width=10).pack(side=tk.LEFT, padx=5)

        tk.Label(grp_paper, text="Hướng:").pack(side=tk.LEFT, padx=(20, 5))
        self.orient_var = tk.StringVar(value="PORTRAIT")
        ttk.Combobox(grp_paper, textvariable=self.orient_var, values=["PORTRAIT", "LANDSCAPE"], width=10).pack(side=tk.LEFT)

        # Margins
        grp_margin = tk.LabelFrame(tab, text="Căn lề (Margins - cm)", fg="blue", padx=10, pady=10)
        grp_margin.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_margin, text="Top:").grid(row=0, column=0)
        self.m_top = tk.StringVar(value="2")
        tk.Entry(grp_margin, textvariable=self.m_top, width=5).grid(row=0, column=1, padx=5, pady=5)

        tk.Label(grp_margin, text="Bottom:").grid(row=0, column=2)
        self.m_bottom = tk.StringVar(value="2")
        tk.Entry(grp_margin, textvariable=self.m_bottom, width=5).grid(row=0, column=3, padx=5, pady=5)

        tk.Label(grp_margin, text="Left:").grid(row=1, column=0)
        self.m_left = tk.StringVar(value="3")
        tk.Entry(grp_margin, textvariable=self.m_left, width=5).grid(row=1, column=1, padx=5, pady=5)

        tk.Label(grp_margin, text="Right:").grid(row=1, column=2)
        self.m_right = tk.StringVar(value="1.5")
        tk.Entry(grp_margin, textvariable=self.m_right, width=5).grid(row=1, column=3, padx=5, pady=5)

        tk.Button(grp_margin, text="⚡ Áp dụng chuẩn Hành chính (NĐ 30)", bg="orange", command=self.apply_admin_std).grid(row=2, column=0, columnspan=4, pady=10)

    def apply_admin_std(self):
        self.m_top.set("2")
        self.m_bottom.set("2")
        self.m_left.set("3")
        self.m_right.set("1.5")
        self.paper_size_var.set("A4")
        self.orient_var.set("PORTRAIT")

    # --- TAB 4: PAGE NUMBERING ---
    def setup_tab_pagenum(self):
        tab = self.tab_pagenum

        grp_pos = tk.LabelFrame(tab, text="Vị trí & Kiểu", fg="blue", padx=10, pady=10)
        grp_pos.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_pos, text="Vị trí: Footer").pack(anchor="w")

        tk.Label(grp_pos, text="Căn lề:").pack(anchor="w", pady=(5,0))
        self.pg_align_var = tk.StringVar(value="CENTER")
        ttk.Combobox(grp_pos, textvariable=self.pg_align_var, values=["LEFT", "CENTER", "RIGHT"], state="readonly").pack(anchor="w")

        grp_logic = tk.LabelFrame(tab, text="Logic đánh số", fg="purple", padx=10, pady=10)
        grp_logic.pack(fill=tk.X, padx=10, pady=5)

        tk.Checkbutton(grp_logic, text="Bắt đầu từ số:", variable=tk.BooleanVar(value=True), state="disabled").grid(row=0, column=0, sticky="w")
        self.pg_start_var = tk.StringVar(value="1")
        tk.Entry(grp_logic, textvariable=self.pg_start_var, width=5).grid(row=0, column=1)

        self.pg_skip_first_var = tk.BooleanVar()
        tk.Checkbutton(grp_logic, text="Bỏ qua trang đầu (Skip First)", variable=self.pg_skip_first_var).grid(row=1, column=0, columnspan=2, sticky="w")

    # --- TAB 5: CLEANING ---
    def setup_tab_clean(self):
        tab = self.tab_clean

        grp_clean = tk.LabelFrame(tab, text="Dọn dẹp văn bản", fg="red", padx=10, pady=10)
        grp_clean.pack(fill=tk.X, padx=10, pady=5)

        self.clean_spaces_var = tk.BooleanVar()
        tk.Checkbutton(grp_clean, text="Xóa khoảng trắng thừa", variable=self.clean_spaces_var).pack(anchor="w")

        self.clean_lines_var = tk.BooleanVar()
        tk.Checkbutton(grp_clean, text="Xóa dòng trống liên tiếp", variable=self.clean_lines_var).pack(anchor="w")

        self.clean_links_var = tk.BooleanVar()
        tk.Checkbutton(grp_clean, text="Xóa Hyperlink", variable=self.clean_links_var).pack(anchor="w")

        grp_img = tk.LabelFrame(tab, text="Xử lý Ảnh", fg="blue", padx=10, pady=10)
        grp_img.pack(fill=tk.X, padx=10, pady=5)

        self.resize_img_var = tk.BooleanVar()
        tk.Checkbutton(grp_img, text="Resize ảnh quá khổ về:", variable=self.resize_img_var).pack(side=tk.LEFT)
        self.img_width_var = tk.StringVar(value="16")
        tk.Entry(grp_img, textvariable=self.img_width_var, width=4).pack(side=tk.LEFT, padx=5)
        tk.Label(grp_img, text="cm").pack(side=tk.LEFT)

    # --- TAB 6: EXPORT ---
    def setup_tab_export(self):
        tab = self.tab_export

        grp_exp = tk.LabelFrame(tab, text="Xuất bản", fg="green", padx=10, pady=10)
        grp_exp.pack(fill=tk.X, padx=10, pady=5)

        self.do_pdf_var = tk.BooleanVar()
        tk.Checkbutton(grp_exp, text="Xuất PDF (Yêu cầu Word cài sẵn)", variable=self.do_pdf_var).pack(anchor="w")

        tk.Label(tab, text="Các tính năng khác (Mục lục, Watermark) đang phát triển...", fg="gray").pack(pady=20)

    # --- ACTION HANDLERS ---

    def process_one_file(self, filepath):
        # Determine Output Path
        if self.create_new_var.get():
            base, ext = os.path.splitext(filepath)
            output_path = f"{base}_fixed{ext}"
        else:
            output_path = filepath

        try:
            self.automator.load_document(filepath)

            # 1. Text Formatting
            self.automator.set_font_style(self.font_name_var.get(), self.font_size_var.get())
            self.automator.set_paragraph_style(self.line_spacing_var.get(), self.space_before_var.get(), self.space_after_var.get())
            self.automator.set_alignment(self.align_var.get())
            self.automator.set_indentation("First Line", self.indent_val_var.get())
            if self.vni_convert_var.get():
                self.automator.convert_vni_to_unicode()

            # 2. Page Setup
            self.automator.set_paper_size(self.paper_size_var.get())
            self.automator.set_orientation_whole_doc(self.orient_var.get())
            self.automator.set_margins(self.m_top.get(), self.m_bottom.get(), self.m_left.get(), self.m_right.get())

            # 3. Page Numbering
            # Only if user selected valid start
            try:
                start = int(self.pg_start_var.get())
            except:
                start = 1
            self.automator.add_page_number(self.pg_align_var.get(), start_at=start, skip_first=self.pg_skip_first_var.get())

            # 4. Cleaning
            if self.clean_spaces_var.get(): self.automator.clean_extra_spaces()
            if self.clean_lines_var.get(): self.automator.clean_empty_lines()
            if self.clean_links_var.get(): self.automator.remove_hyperlinks()
            if self.resize_img_var.get(): self.automator.resize_images(self.img_width_var.get())

            # Save
            self.automator.save_document(output_path)

            # 5. Export PDF
            if self.do_pdf_var.get():
                try:
                    self.automator.export_to_pdf()
                except Exception as e:
                    print(f"PDF Error: {e}") # Log only, don't stop batch

            return output_path

        except Exception as e:
            raise e

    def apply_all_changes(self):
        path = self.file_path_var.get()
        if not path:
            messagebox.showwarning("Cảnh báo", "Chưa chọn file/folder!")
            return

        is_folder = self.is_folder_var.get()
        files = []

        if is_folder:
            if not os.path.isdir(path):
                messagebox.showerror("Lỗi", "Thư mục không tồn tại!")
                return
            for root_dir, dirs, filenames in os.walk(path):
                for f in filenames:
                    if f.endswith(".docx") and not f.startswith("~$"):
                        files.append(os.path.join(root_dir, f))
        else:
            if not os.path.isfile(path):
                messagebox.showerror("Lỗi", "File không tồn tại!")
                return
            files.append(path)

        if not files:
            messagebox.showinfo("Thông báo", "Không tìm thấy file .docx nào!")
            return

        # Confirm
        if not messagebox.askyesno("Xác nhận", f"Tìm thấy {len(files)} file. Bắt đầu xử lý?"):
            return

        # Processing Loop
        success_count = 0
        errors = []
        last_file = None

        for f in files:
            try:
                out = self.process_one_file(f)
                success_count += 1
                last_file = out
            except Exception as e:
                errors.append(f"{os.path.basename(f)}: {str(e)}")

        # Report
        msg = f"Hoàn thành: {success_count}/{len(files)}"
        if errors:
            msg += "\n\nLỗi:\n" + "\n".join(errors[:5])
            if len(errors) > 5: msg += "\n..."
            messagebox.showwarning("Kết quả", msg)
        else:
            messagebox.showinfo("Thành công", msg)
            if self.open_after_var.get() and last_file and len(files) == 1:
                if platform.system() == "Windows":
                    os.startfile(last_file)

if __name__ == "__main__":
    root = tk.Tk()
    app = OfficeApp(root)
    root.mainloop()
