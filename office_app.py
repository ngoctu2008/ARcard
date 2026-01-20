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
        self.root.geometry("850x750")

        self.automator = OfficeAutomator()

        # State variables
        self.file_path_var = tk.StringVar()
        self.is_folder_var = tk.BooleanVar(value=False)
        self.scope_var = tk.StringVar(value="ALL")

        self.create_new_var = tk.BooleanVar(value=True)
        self.open_after_var = tk.BooleanVar(value=True)

        # Theme Setup
        self.setup_theme()
        self.setup_ui()

    def setup_theme(self):
        style = ttk.Style(self.root)
        try:
            style.theme_use('clam')
        except:
            pass # Fallback to default if unavailable

        # Color Palette
        bg_color = "#f4f6f7"
        primary_color = "#2980b9" # Strong Blue
        secondary_color = "#ecf0f1" # Light Gray
        accent_color = "#27ae60" # Green
        text_color = "#2c3e50"

        self.root.configure(bg=bg_color)

        # Notebook Style
        style.configure("TNotebook", background=bg_color, tabposition='n')
        style.configure("TNotebook.Tab", padding=[15, 5], font=("Arial", 10))
        style.map("TNotebook.Tab",
            background=[("selected", primary_color), ("!selected", "#bdc3c7")],
            foreground=[("selected", "white"), ("!selected", "#2c3e50")]
        )

        # Frame Style
        style.configure("TFrame", background=bg_color)

        # Label Style
        style.configure("TLabel", background=bg_color, foreground=text_color, font=("Arial", 10))

        # Labelframe Style
        style.configure("TLabelframe", background=bg_color, foreground=primary_color)
        style.configure("TLabelframe.Label", background=bg_color, foreground=primary_color, font=("Arial", 10, "bold"))

        # Button Style
        style.configure("TButton", font=("Arial", 10), padding=5)
        style.map("TButton",
            background=[("active", "#3498db")],
            foreground=[("active", "black")]
        )

        # Checkbutton & Radiobutton
        style.configure("TCheckbutton", background=bg_color, font=("Arial", 10))
        style.configure("TRadiobutton", background=bg_color, font=("Arial", 10))

        # Entry
        style.configure("TEntry", padding=5)

    def setup_ui(self):
        # Header
        header_frame = tk.Frame(self.root, bg="#2c3e50")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="TỰ ĐỘNG THIẾT LẬP CHO OFFICE",
                 bg="#2c3e50", fg="white", font=("Segoe UI", 16, "bold"), pady=15).pack(side=tk.LEFT, padx=20)
        tk.Label(header_frame, text="v4.0 (Modern UI)",
                 bg="#2c3e50", fg="#bdc3c7", font=("Arial", 9)).pack(side=tk.RIGHT, padx=20)

        # Tabs
        self.notebook = ttk.Notebook(self.root)
        self.notebook.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        self.tab_general = ttk.Frame(self.notebook)
        self.tab_text = ttk.Frame(self.notebook)
        self.tab_page = ttk.Frame(self.notebook)
        self.tab_pagenum = ttk.Frame(self.notebook)
        self.tab_security = ttk.Frame(self.notebook)
        self.tab_clean = ttk.Frame(self.notebook)
        self.tab_export = ttk.Frame(self.notebook)

        self.notebook.add(self.tab_general, text="Cấu hình Chung")
        self.notebook.add(self.tab_text, text="Định dạng Văn bản")
        self.notebook.add(self.tab_page, text="Thiết lập Trang")
        self.notebook.add(self.tab_pagenum, text="Đánh số trang")
        self.notebook.add(self.tab_security, text="Bảo mật")
        self.notebook.add(self.tab_clean, text="Dọn dẹp")
        self.notebook.add(self.tab_export, text="Xuất bản")

        self.setup_tab_general()
        self.setup_tab_text()
        self.setup_tab_page()
        self.setup_tab_pagenum()
        self.setup_tab_security()
        self.setup_tab_clean()
        self.setup_tab_export()

        # Footer Buttons
        footer_frame = ttk.Frame(self.root)
        footer_frame.pack(fill=tk.X, side=tk.BOTTOM, pady=10)

        # Customizing standard buttons for distinct actions
        btn_apply = tk.Button(footer_frame, text="✔ ÁP DỤNG TẤT CẢ", bg="#27ae60", fg="white",
                              font=("Arial", 11, "bold"), width=25, height=2, bd=0, command=self.apply_all_changes)
        btn_apply.pack(side=tk.LEFT, padx=50)

        btn_exit = tk.Button(footer_frame, text="❌ THOÁT", bg="#e74c3c", fg="white",
                             font=("Arial", 11, "bold"), width=15, height=2, bd=0, command=self.root.quit)
        btn_exit.pack(side=tk.RIGHT, padx=50)

    # --- TAB 1: GENERAL ---
    def setup_tab_general(self):
        tab = self.tab_general

        grp_scope = ttk.LabelFrame(tab, text="Phạm vi áp dụng (Scope)", padding=15)
        grp_scope.pack(fill=tk.X, padx=15, pady=10)

        f_file = ttk.Frame(grp_scope)
        f_file.pack(fill=tk.X)
        ttk.Entry(f_file, textvariable=self.file_path_var).pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 10))
        ttk.Button(f_file, text="📂 Chọn...", command=self.browse_file).pack(side=tk.LEFT)

        f_opts = ttk.Frame(grp_scope, padding=(0, 10, 0, 0))
        f_opts.pack(fill=tk.X)
        ttk.Checkbutton(f_opts, text="Xử lý cả thư mục (Folder)", variable=self.is_folder_var, command=self.on_mode_change).pack(side=tk.LEFT)

        ttk.Label(f_opts, text="|").pack(side=tk.LEFT, padx=10)
        ttk.Radiobutton(f_opts, text="Toàn bộ văn bản", variable=self.scope_var, value="ALL").pack(side=tk.LEFT)
        ttk.Radiobutton(f_opts, text="Chỉ Section (Sim)", variable=self.scope_var, value="SECTION", state="disabled").pack(side=tk.LEFT, padx=10)

        grp_view = ttk.LabelFrame(tab, text="Tùy chọn hiển thị (Word Settings - Simulated)", padding=15)
        grp_view.pack(fill=tk.X, padx=15, pady=10)
        ttk.Checkbutton(grp_view, text="Tắt Protected View").grid(row=0, column=0, sticky="w", padx=10)
        ttk.Checkbutton(grp_view, text="Tắt Start Screen").grid(row=0, column=1, sticky="w", padx=10)

        grp_post = ttk.LabelFrame(tab, text="Hành động sau xử lý", padding=15)
        grp_post.pack(fill=tk.X, padx=15, pady=10)
        ttk.Checkbutton(grp_post, text="Lưu thành file mới (Backup file gốc)", variable=self.create_new_var).pack(anchor="w")
        ttk.Checkbutton(grp_post, text="Mở file sau khi làm xong", variable=self.open_after_var).pack(anchor="w")

    def on_mode_change(self):
        self.file_path_var.set("")

    def browse_file(self):
        if self.is_folder_var.get():
            path = filedialog.askdirectory()
        else:
            path = filedialog.askopenfilename(filetypes=[("Word Documents", "*.docx")])
        if path: self.file_path_var.set(path)

    # --- TAB 2: TEXT FORMATTING ---
    def setup_tab_text(self):
        tab = self.tab_text

        grp_font = ttk.LabelFrame(tab, text="Font chữ (Typography)", padding=15)
        grp_font.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_font, text="Font:").grid(row=0, column=0, sticky="w")
        self.font_name_var = tk.StringVar(value="Times New Roman")
        ttk.Combobox(grp_font, textvariable=self.font_name_var, values=["Times New Roman", "Arial", "Calibri"], width=20).grid(row=0, column=1, padx=10)

        ttk.Label(grp_font, text="Size:").grid(row=0, column=2, sticky="e")
        self.font_size_var = tk.StringVar(value="14")
        tk.Spinbox(grp_font, textvariable=self.font_size_var, from_=8, to=72, width=5).grid(row=0, column=3, padx=10)

        self.vni_convert_var = tk.BooleanVar()
        ttk.Checkbutton(grp_font, text="Chuyển mã VNI -> Unicode (Basic)", variable=self.vni_convert_var).grid(row=1, column=0, columnspan=4, sticky="w", pady=(10,0))

        grp_para = ttk.LabelFrame(tab, text="Đoạn văn (Paragraph)", padding=15)
        grp_para.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_para, text="Căn lề:").grid(row=0, column=0, sticky="w", pady=5)
        self.align_var = tk.StringVar(value="JUSTIFY")
        ttk.Combobox(grp_para, textvariable=self.align_var, values=["LEFT", "CENTER", "RIGHT", "JUSTIFY"], state="readonly", width=15).grid(row=0, column=1, padx=10)

        ttk.Label(grp_para, text="Line Spacing:").grid(row=1, column=0, sticky="w", pady=5)
        self.line_spacing_var = tk.StringVar(value="1.5")
        ttk.Combobox(grp_para, textvariable=self.line_spacing_var, values=["1.0", "1.15", "1.5"], width=10).grid(row=1, column=1, padx=10)

        ttk.Label(grp_para, text="Before (pt):").grid(row=0, column=2, sticky="e")
        self.space_before_var = tk.StringVar(value="3")
        ttk.Entry(grp_para, textvariable=self.space_before_var, width=5).grid(row=0, column=3, padx=10)

        ttk.Label(grp_para, text="After (pt):").grid(row=1, column=2, sticky="e")
        self.space_after_var = tk.StringVar(value="3")
        ttk.Entry(grp_para, textvariable=self.space_after_var, width=5).grid(row=1, column=3, padx=10)

        ttk.Label(grp_para, text="Thụt dòng:").grid(row=2, column=0, sticky="w", pady=5)
        self.indent_val_var = tk.StringVar(value="1.27")
        f_ind = ttk.Frame(grp_para)
        f_ind.grid(row=2, column=1, columnspan=3, sticky="w", padx=10)
        ttk.Entry(f_ind, textvariable=self.indent_val_var, width=8).pack(side=tk.LEFT)
        ttk.Label(f_ind, text="cm (First Line)").pack(side=tk.LEFT, padx=5)

    # --- TAB 3: PAGE SETUP ---
    def setup_tab_page(self):
        tab = self.tab_page

        grp_paper = ttk.LabelFrame(tab, text="Khổ giấy (Paper)", padding=15)
        grp_paper.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_paper, text="Size:").pack(side=tk.LEFT)
        self.paper_size_var = tk.StringVar(value="A4")
        ttk.Combobox(grp_paper, textvariable=self.paper_size_var, values=["A4", "A3", "Letter"], width=15).pack(side=tk.LEFT, padx=10)

        ttk.Label(grp_paper, text="Hướng:").pack(side=tk.LEFT, padx=(20, 0))
        self.orient_var = tk.StringVar(value="PORTRAIT")
        ttk.Combobox(grp_paper, textvariable=self.orient_var, values=["PORTRAIT", "LANDSCAPE"], width=15).pack(side=tk.LEFT, padx=10)

        grp_margin = ttk.LabelFrame(tab, text="Căn lề (Margins - cm)", padding=15)
        grp_margin.pack(fill=tk.X, padx=15, pady=10)

        grid_frame = ttk.Frame(grp_margin)
        grid_frame.pack()

        ttk.Label(grid_frame, text="Top:").grid(row=0, column=0, padx=5, pady=5)
        self.m_top = tk.StringVar(value="2")
        ttk.Entry(grid_frame, textvariable=self.m_top, width=5).grid(row=0, column=1)

        ttk.Label(grid_frame, text="Bottom:").grid(row=0, column=2, padx=5, pady=5)
        self.m_bottom = tk.StringVar(value="2")
        ttk.Entry(grid_frame, textvariable=self.m_bottom, width=5).grid(row=0, column=3)

        ttk.Label(grid_frame, text="Left:").grid(row=1, column=0, padx=5, pady=5)
        self.m_left = tk.StringVar(value="3")
        ttk.Entry(grid_frame, textvariable=self.m_left, width=5).grid(row=1, column=1)

        ttk.Label(grid_frame, text="Right:").grid(row=1, column=2, padx=5, pady=5)
        self.m_right = tk.StringVar(value="1.5")
        ttk.Entry(grid_frame, textvariable=self.m_right, width=5).grid(row=1, column=3)

    # --- TAB 4: PAGE NUMBERING ---
    def setup_tab_pagenum(self):
        tab = self.tab_pagenum

        grp_pos = ttk.LabelFrame(tab, text="Vị trí & Kiểu", padding=15)
        grp_pos.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_pos, text="Vị trí: Footer (Mặc định)").pack(anchor="w")
        ttk.Label(grp_pos, text="Căn lề:").pack(anchor="w", pady=(10,0))
        self.pg_align_var = tk.StringVar(value="CENTER")
        ttk.Combobox(grp_pos, textvariable=self.pg_align_var, values=["LEFT", "CENTER", "RIGHT"], state="readonly", width=15).pack(anchor="w")

        grp_logic = ttk.LabelFrame(tab, text="Logic đánh số", padding=15)
        grp_logic.pack(fill=tk.X, padx=15, pady=10)

        f_start = ttk.Frame(grp_logic)
        f_start.pack(anchor="w")
        ttk.Label(f_start, text="Start at:").pack(side=tk.LEFT)
        self.pg_start_var = tk.StringVar(value="1")
        ttk.Entry(f_start, textvariable=self.pg_start_var, width=5).pack(side=tk.LEFT, padx=10)

        self.pg_skip_first_var = tk.BooleanVar()
        ttk.Checkbutton(grp_logic, text="Bỏ qua trang đầu (Different First Page)", variable=self.pg_skip_first_var).pack(anchor="w", pady=(10,0))

    # --- TAB 5: SECURITY ---
    def setup_tab_security(self):
        tab = self.tab_security

        grp_hf = ttk.LabelFrame(tab, text="Đầu trang & Chân trang (Global)", padding=15)
        grp_hf.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_hf, text="Header:").grid(row=0, column=0, sticky="w", pady=5)
        self.header_text_var = tk.StringVar()
        ttk.Entry(grp_hf, textvariable=self.header_text_var, width=40).grid(row=0, column=1, padx=10)

        ttk.Label(grp_hf, text="Footer:").grid(row=1, column=0, sticky="w", pady=5)
        self.footer_text_var = tk.StringVar()
        ttk.Entry(grp_hf, textvariable=self.footer_text_var, width=40).grid(row=1, column=1, padx=10)

        grp_info = ttk.LabelFrame(tab, text="Metadata & Thông tin", padding=15)
        grp_info.pack(fill=tk.X, padx=15, pady=10)

        ttk.Label(grp_info, text="Tác giả:").grid(row=0, column=0, sticky="w")
        self.author_var = tk.StringVar(value="Admin")
        ttk.Entry(grp_info, textvariable=self.author_var, width=20).grid(row=0, column=1, padx=10)

        ttk.Label(grp_info, text="Tiêu đề:").grid(row=0, column=2, sticky="w")
        self.title_var = tk.StringVar()
        ttk.Entry(grp_info, textvariable=self.title_var, width=20).grid(row=0, column=3, padx=10)

        self.remove_personal_var = tk.BooleanVar()
        ttk.Checkbutton(grp_info, text="Xóa thông tin cá nhân (Remove Personal Info)", variable=self.remove_personal_var).grid(row=1, column=0, columnspan=4, sticky="w", pady=(10,0))

        grp_lock = ttk.LabelFrame(tab, text="Chế độ Bảo mật", padding=15)
        grp_lock.pack(fill=tk.X, padx=15, pady=10)

        self.lock_mode_var = tk.StringVar(value="NONE")
        ttk.Radiobutton(grp_lock, text="Mở khóa hoàn toàn (Edit)", variable=self.lock_mode_var, value="NONE").pack(anchor="w")
        ttk.Radiobutton(grp_lock, text="KHÓA TUYỆT ĐỐI (Read Only)", variable=self.lock_mode_var, value="READ_ONLY").pack(anchor="w")

    # --- TAB 6: CLEANING ---
    def setup_tab_clean(self):
        tab = self.tab_clean

        grp_clean = ttk.LabelFrame(tab, text="Dọn dẹp văn bản", padding=15)
        grp_clean.pack(fill=tk.X, padx=15, pady=10)

        self.clean_spaces_var = tk.BooleanVar()
        ttk.Checkbutton(grp_clean, text="Xóa khoảng trắng thừa", variable=self.clean_spaces_var).pack(anchor="w", pady=2)

        self.clean_lines_var = tk.BooleanVar()
        ttk.Checkbutton(grp_clean, text="Xóa dòng trống liên tiếp", variable=self.clean_lines_var).pack(anchor="w", pady=2)

        self.clean_links_var = tk.BooleanVar()
        ttk.Checkbutton(grp_clean, text="Xóa Hyperlink", variable=self.clean_links_var).pack(anchor="w", pady=2)

        grp_img = ttk.LabelFrame(tab, text="Xử lý Ảnh", padding=15)
        grp_img.pack(fill=tk.X, padx=15, pady=10)

        self.resize_img_var = tk.BooleanVar()
        f_resize = ttk.Frame(grp_img)
        f_resize.pack(anchor="w")
        ttk.Checkbutton(f_resize, text="Resize ảnh quá khổ về:", variable=self.resize_img_var).pack(side=tk.LEFT)
        self.img_width_var = tk.StringVar(value="16")
        ttk.Entry(f_resize, textvariable=self.img_width_var, width=5).pack(side=tk.LEFT, padx=5)
        ttk.Label(f_resize, text="cm").pack(side=tk.LEFT)

    # --- TAB 7: EXPORT ---
    def setup_tab_export(self):
        tab = self.tab_export

        grp_exp = ttk.LabelFrame(tab, text="Chuyển đổi & Xuất bản", padding=15)
        grp_exp.pack(fill=tk.X, padx=15, pady=10)

        self.do_toc_var = tk.BooleanVar()
        ttk.Checkbutton(grp_exp, text="Tạo Mục lục (Table of Contents) ở trang đầu", variable=self.do_toc_var).pack(anchor="w", pady=5)
        ttk.Label(grp_exp, text="(Lưu ý: Sau khi mở file, cần chuột phải TOC -> Update Field)", font=("Arial", 9, "italic"), foreground="gray").pack(anchor="w", padx=20)

        self.do_pdf_var = tk.BooleanVar()
        ttk.Checkbutton(grp_exp, text="Xuất sang PDF (Yêu cầu MS Word cài sẵn)", variable=self.do_pdf_var).pack(anchor="w", pady=(15, 5))

    # --- ACTION HANDLERS ---

    def process_one_file(self, filepath):
        if self.create_new_var.get():
            base, ext = os.path.splitext(filepath)
            output_path = f"{base}_fixed{ext}"
        else:
            output_path = filepath

        try:
            self.automator.load_document(filepath)

            # 0. TOC (Insert first so it's at top)
            if self.do_toc_var.get():
                try: self.automator.add_table_of_contents()
                except: pass

            # 1. Text Formatting
            self.automator.set_font_style(self.font_name_var.get(), self.font_size_var.get())
            self.automator.set_paragraph_style(self.line_spacing_var.get(), self.space_before_var.get(), self.space_after_var.get())
            self.automator.set_alignment(self.align_var.get())
            self.automator.set_indentation("First Line", self.indent_val_var.get())
            if self.vni_convert_var.get(): self.automator.convert_vni_to_unicode()

            # 2. Page Setup
            self.automator.set_paper_size(self.paper_size_var.get())
            self.automator.set_orientation_whole_doc(self.orient_var.get())
            self.automator.set_margins(self.m_top.get(), self.m_bottom.get(), self.m_left.get(), self.m_right.get())

            # 3. Page Numbering
            try: start = int(self.pg_start_var.get())
            except: start = 1
            self.automator.add_page_number(self.pg_align_var.get(), start_at=start, skip_first=self.pg_skip_first_var.get())

            # 4. Security
            self.automator.set_header_footer_text(
                header_text=self.header_text_var.get() or None,
                footer_text=self.footer_text_var.get() or None
            )
            self.automator.set_file_properties(
                author=self.author_var.get(),
                title=self.title_var.get(),
                remove_personal_info=self.remove_personal_var.get()
            )
            self.automator.protect_document(
                protection_type=self.lock_mode_var.get()
            )

            # 5. Cleaning
            if self.clean_spaces_var.get(): self.automator.clean_extra_spaces()
            if self.clean_lines_var.get(): self.automator.clean_empty_lines()
            if self.clean_links_var.get(): self.automator.remove_hyperlinks()
            if self.resize_img_var.get(): self.automator.resize_images(self.img_width_var.get())

            # Save
            self.automator.save_document(output_path)

            # 6. Export PDF
            if self.do_pdf_var.get():
                try: self.automator.export_to_pdf()
                except Exception: pass

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

        if not messagebox.askyesno("Xác nhận", f"Tìm thấy {len(files)} file. Bắt đầu xử lý?"):
            return

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

        msg = f"Hoàn thành: {success_count}/{len(files)}"
        if errors:
            msg += "\n\nLỗi:\n" + "\n".join(errors[:5])
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
