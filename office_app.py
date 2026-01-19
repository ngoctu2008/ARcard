import tkinter as tk
from tkinter import messagebox
import os
import office_lib

class TemplateInstallerApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Cài đặt Template Word")
        self.root.geometry("500x400")

        # Container
        main_frame = tk.Frame(root, padx=20, pady=20)
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Input Fields
        tk.Label(main_frame, text="Địa chỉ tổ chức:").pack(anchor=tk.W)
        self.address_entry = tk.Entry(main_frame, width=50)
        self.address_entry.pack(pady=(0, 10), fill=tk.X)

        tk.Label(main_frame, text="Chức danh người đại diện:").pack(anchor=tk.W)
        self.title_entry = tk.Entry(main_frame, width=50)
        self.title_entry.pack(pady=(0, 20), fill=tk.X)

        # Action Button
        self.install_btn = tk.Button(main_frame, text="Cài đặt Template", command=self.install_templates, bg="#4CAF50", fg="white")
        self.install_btn.pack(pady=(0, 20), fill=tk.X)

        # Output List
        tk.Label(main_frame, text="Danh sách văn bản đã cài đặt:").pack(anchor=tk.W)
        self.file_listbox = tk.Listbox(main_frame)
        self.file_listbox.pack(fill=tk.BOTH, expand=True)
        self.file_listbox.bind('<Double-1>', self.open_file)

        # Load existing files
        self.refresh_file_list()

    def install_templates(self):
        address = self.address_entry.get().strip()
        title = self.title_entry.get().strip()

        if not address or not title:
            messagebox.showwarning("Thiếu thông tin", "Vui lòng nhập đầy đủ địa chỉ và chức danh.")
            return

        data = {
            "DIA_CHI": address,
            "CHUC_DANH": title
        }

        try:
            generated = office_lib.process_templates(data)
            self.refresh_file_list()
            messagebox.showinfo("Thành công", f"Đã cài đặt {len(generated)} văn bản.")
        except Exception as e:
            messagebox.showerror("Lỗi", str(e))

    def refresh_file_list(self):
        self.file_listbox.delete(0, tk.END)
        files = office_lib.get_installed_documents()
        for f in files:
            self.file_listbox.insert(tk.END, f)

    def open_file(self, event):
        selection = self.file_listbox.curselection()
        if selection:
            filename = self.file_listbox.get(selection[0])
            filepath = os.path.join(office_lib.OUTPUT_DIR, filename)
            # Cross-platform open
            try:
                if os.name == 'nt':  # Windows
                    os.startfile(filepath)
                else:  # Linux/Mac
                    import subprocess
                    subprocess.call(('xdg-open', filepath))
            except Exception as e:
                print(f"Cannot open file: {e}")

if __name__ == "__main__":
    root = tk.Tk()
    app = TemplateInstallerApp(root)
    root.mainloop()
