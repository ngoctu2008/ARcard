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
        self.scope_var = tk.StringVar(value="ALL")

        self.create_new_var = tk.BooleanVar(value=True)
        self.open_after_var = tk.BooleanVar(value=True)

        self.setup_ui()

    def setup_ui(self):
        # Header
        header_frame = tk.Frame(self.root, bg="#007bff")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="TỰ ĐỘNG THIẾT LẬP CHO OFFICE",
                 bg="#007bff", fg="white", font=("Arial", 14, "bold"), pady=10).pack()
        tk.Label(header_frame, text="version 3.1",
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
        self.tab_security = ttk.Frame(self.notebook) # New Tab

        self.notebook.add(self.tab_general, text="Cấu hình Chung")
        self.notebook.add(self.tab_text, text="Định dạng Văn bản")
        self.notebook.add(self.tab_page, text="Thiết lập Trang")
        self.notebook.add(self.tab_pagenum, text="Đánh số trang")
        self.notebook.add(self.tab_security, text="Bảo mật & Thông tin") # Inserted here
        self.notebook.add(self.tab_clean, text="Dọn dẹp & Tiện ích")
        self.notebook.add(self.tab_export, text="Mục lục & Xuất bản")

        self.setup_tab_general()
        self.setup_tab_text()
        self.setup_tab_page()
        self.setup_tab_pagenum()
        self.setup_tab_security() # New Setup
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

        grp_view = tk.LabelFrame(tab, text="Tùy chọn hiển thị (Word Settings)", fg="green", padx=10, pady=10)
        grp_view.pack(fill=tk.X, padx=10, pady=5)
        tk.Checkbutton(grp_view, text="Tắt Protected View (Giả lập)").grid(row=0, column=0, sticky="w")
        tk.Checkbutton(grp_view, text="Tắt Start Screen (Giả lập)").grid(row=0, column=1, sticky="w")

        grp_post = tk.LabelFrame(tab, text="Hành động sau xử lý", fg="purple", padx=10, pady=10)
        grp_post.pack(fill=tk.X, padx=10, pady=5)
        tk.Checkbutton(grp_post, text="Lưu thành file mới (Backup file gốc)", variable=self.create_new_var).pack(anchor="w")
        tk.Checkbutton(grp_post, text="Mở file sau khi làm xong", variable=self.open_after_var).pack(anchor="w")

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
        grp_font = tk.LabelFrame(tab, text="Font chữ (Typography)", fg="blue", padx=10, pady=10)
        grp_font.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_font, text="Font:").grid(row=0, column=0, sticky="w")
        self.font_name_var = tk.StringVar(value="Times New Roman")
        ttk.Combobox(grp_font, textvariable=self.font_name_var, values=["Times New Roman", "Arial", "Calibri"]).grid(row=0, column=1)
        tk.Label(grp_font, text="Size:").grid(row=0, column=2, sticky="e", padx=10)
        self.font_size_var = tk.StringVar(value="14")
        tk.Spinbox(grp_font, textvariable=self.font_size_var, from_=8, to=72, width=5).grid(row=0, column=3)
        self.vni_convert_var = tk.BooleanVar()
        tk.Checkbutton(grp_font, text="Chuyển mã VNI -> Unicode (Basic)", variable=self.vni_convert_var).grid(row=1, column=0, columnspan=4, sticky="w", pady=5)

        grp_para = tk.LabelFrame(tab, text="Đoạn văn (Paragraph)", fg="blue", padx=10, pady=10)
        grp_para.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_para, text="Căn lề:").grid(row=0, column=0, sticky="w")
        self.align_var = tk.StringVar(value="JUSTIFY")
        ttk.Combobox(grp_para, textvariable=self.align_var, values=["LEFT", "CENTER", "RIGHT", "JUSTIFY"], state="readonly", width=10).grid(row=0, column=1)
        tk.Label(grp_para, text="Line Spacing:").grid(row=1, column=0, sticky="w")
        self.line_spacing_var = tk.StringVar(value="1.5")
        ttk.Combobox(grp_para, textvariable=self.line_spacing_var, values=["1.0", "1.15", "1.5"], width=5).grid(row=1, column=1)
        tk.Label(grp_para, text="Before (pt):").grid(row=0, column=2)
        self.space_before_var = tk.StringVar(value="3")
        tk.Entry(grp_para, textvariable=self.space_before_var, width=5).grid(row=0, column=3)
        tk.Label(grp_para, text="After (pt):").grid(row=1, column=2)
        self.space_after_var = tk.StringVar(value="3")
        tk.Entry(grp_para, textvariable=self.space_after_var, width=5).grid(row=1, column=3)
        tk.Label(grp_para, text="Thụt dòng:").grid(row=2, column=0)
        self.indent_val_var = tk.StringVar(value="1.27")
        tk.Entry(grp_para, textvariable=self.indent_val_var, width=5).grid(row=2, column=1)

    # --- TAB 3: PAGE SETUP ---
    def setup_tab_page(self):
        tab = self.tab_page
        grp_paper = tk.LabelFrame(tab, text="Khổ giấy (Paper)", fg="blue", padx=10, pady=10)
        grp_paper.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_paper, text="Size:").pack(side=tk.LEFT)
        self.paper_size_var = tk.StringVar(value="A4")
        ttk.Combobox(grp_paper, textvariable=self.paper_size_var, values=["A4", "A3", "Letter"], width=10).pack(side=tk.LEFT)
        tk.Label(grp_paper, text="Hướng:").pack(side=tk.LEFT, padx=10)
        self.orient_var = tk.StringVar(value="PORTRAIT")
        ttk.Combobox(grp_paper, textvariable=self.orient_var, values=["PORTRAIT", "LANDSCAPE"], width=10).pack(side=tk.LEFT)

        grp_margin = tk.LabelFrame(tab, text="Căn lề (Margins - cm)", fg="blue", padx=10, pady=10)
        grp_margin.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_margin, text="Top:").grid(row=0, column=0)
        self.m_top = tk.StringVar(value="2")
        tk.Entry(grp_margin, textvariable=self.m_top, width=5).grid(row=0, column=1)
        tk.Label(grp_margin, text="Bottom:").grid(row=0, column=2)
        self.m_bottom = tk.StringVar(value="2")
        tk.Entry(grp_margin, textvariable=self.m_bottom, width=5).grid(row=0, column=3)
        tk.Label(grp_margin, text="Left:").grid(row=1, column=0)
        self.m_left = tk.StringVar(value="3")
        tk.Entry(grp_margin, textvariable=self.m_left, width=5).grid(row=1, column=1)
        tk.Label(grp_margin, text="Right:").grid(row=1, column=2)
        self.m_right = tk.StringVar(value="1.5")
        tk.Entry(grp_margin, textvariable=self.m_right, width=5).grid(row=1, column=3)

    # --- TAB 4: PAGE NUMBERING ---
    def setup_tab_pagenum(self):
        tab = self.tab_pagenum
        grp_pos = tk.LabelFrame(tab, text="Vị trí & Kiểu", fg="blue", padx=10, pady=10)
        grp_pos.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_pos, text="Vị trí: Footer").pack(anchor="w")
        self.pg_align_var = tk.StringVar(value="CENTER")
        ttk.Combobox(grp_pos, textvariable=self.pg_align_var, values=["LEFT", "CENTER", "RIGHT"], state="readonly").pack(anchor="w")

        grp_logic = tk.LabelFrame(tab, text="Logic đánh số", fg="purple", padx=10, pady=10)
        grp_logic.pack(fill=tk.X, padx=10, pady=5)
        tk.Label(grp_logic, text="Start at:").pack(side=tk.LEFT)
        self.pg_start_var = tk.StringVar(value="1")
        tk.Entry(grp_logic, textvariable=self.pg_start_var, width=5).pack(side=tk.LEFT)
        self.pg_skip_first_var = tk.BooleanVar()
        tk.Checkbutton(grp_logic, text="Bỏ qua trang đầu", variable=self.pg_skip_first_var).pack(side=tk.LEFT, padx=10)

    # --- TAB 5: SECURITY (NEW) ---
    def setup_tab_security(self):
        tab = self.tab_security

        # Header/Footer Text
        grp_hf = tk.LabelFrame(tab, text="Đầu trang - Chân trang", fg="#007bff", padx=10, pady=10)
        grp_hf.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_hf, text="Header:").grid(row=0, column=0, sticky="w")
        self.header_text_var = tk.StringVar()
        tk.Entry(grp_hf, textvariable=self.header_text_var, width=50).grid(row=0, column=1, padx=5, pady=2)

        tk.Label(grp_hf, text="Footer:").grid(row=1, column=0, sticky="w")
        self.footer_text_var = tk.StringVar()
        tk.Entry(grp_hf, textvariable=self.footer_text_var, width=50).grid(row=1, column=1, padx=5, pady=2)

        # File Info
        grp_info = tk.LabelFrame(tab, text="Thông tin tập tin", fg="gray", padx=10, pady=10)
        grp_info.pack(fill=tk.X, padx=10, pady=5)

        tk.Label(grp_info, text="Tác giả:").grid(row=0, column=0, sticky="w")
        self.author_var = tk.StringVar(value="Admin")
        tk.Entry(grp_info, textvariable=self.author_var, width=20).grid(row=0, column=1, sticky="w", padx=5)

        tk.Label(grp_info, text="Tiêu đề:").grid(row=0, column=2, sticky="w", padx=10)
        self.title_var = tk.StringVar()
        tk.Entry(grp_info, textvariable=self.title_var, width=20).grid(row=0, column=3, sticky="w", padx=5)

        self.remove_personal_var = tk.BooleanVar()
        tk.Checkbutton(grp_info, text="Xóa sạch thông tin cá nhân cũ (Remove Personal Info)", variable=self.remove_personal_var).grid(row=1, column=0, columnspan=4, sticky="w", pady=5)

        # Protection
        grp_lock = tk.LabelFrame(tab, text="Chế độ Bảo mật - Khóa", fg="#d9534f", padx=10, pady=10)
        grp_lock.pack(fill=tk.X, padx=10, pady=5)

        self.lock_mode_var = tk.StringVar(value="NONE")
        tk.Radiobutton(grp_lock, text="Mở khóa hoàn toàn (Cho phép chỉnh sửa tự do)", variable=self.lock_mode_var, value="NONE").pack(anchor="w")
        tk.Radiobutton(grp_lock, text="Chỉ khóa Header/Footer (Chưa hỗ trợ - dùng Khóa tuyệt đối)", variable=self.lock_mode_var, value="PARTIAL", state="disabled").pack(anchor="w")
        tk.Radiobutton(grp_lock, text="KHÓA TUYỆT ĐỐI (Chỉ xem - Không sao chép - Không sửa)", variable=self.lock_mode_var, value="READ_ONLY", font=("Arial", 9, "bold")).pack(anchor="w")

        f_pass = tk.Frame(grp_lock, pady=5)
        f_pass.pack(fill=tk.X)
        tk.Label(f_pass, text="🔑 Mật khẩu quản trị:").pack(side=tk.LEFT)
        self.password_var = tk.StringVar()
        tk.Entry(f_pass, textvariable=self.password_var, show="*", width=20, state="disabled").pack(side=tk.LEFT, padx=5) # Disabled because simple protection doesn't set password hash
        tk.Label(f_pass, text="(Mặc định không đặt mật khẩu để tránh lỗi mã hóa)", fg="gray", font=("Arial", 8)).pack(side=tk.LEFT)

    # --- TAB 6: CLEANING ---
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

    # --- TAB 7: EXPORT ---
    def setup_tab_export(self):
        tab = self.tab_export
        grp_exp = tk.LabelFrame(tab, text="Xuất bản", fg="green", padx=10, pady=10)
        grp_exp.pack(fill=tk.X, padx=10, pady=5)
        self.do_pdf_var = tk.BooleanVar()
        tk.Checkbutton(grp_exp, text="Xuất PDF (Yêu cầu Word cài sẵn)", variable=self.do_pdf_var).pack(anchor="w")

    # --- ACTION HANDLERS ---

    def process_one_file(self, filepath):
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
            if self.vni_convert_var.get(): self.automator.convert_vni_to_unicode()

            # 2. Page Setup
            self.automator.set_paper_size(self.paper_size_var.get())
            self.automator.set_orientation_whole_doc(self.orient_var.get())
            self.automator.set_margins(self.m_top.get(), self.m_bottom.get(), self.m_left.get(), self.m_right.get())

            # 3. Page Numbering
            try: start = int(self.pg_start_var.get())
            except: start = 1
            self.automator.add_page_number(self.pg_align_var.get(), start_at=start, skip_first=self.pg_skip_first_var.get())

            # 4. Security & Info (NEW)
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
                protection_type=self.lock_mode_var.get(),
                password=self.password_var.get()
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
