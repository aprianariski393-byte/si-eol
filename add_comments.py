
import os
import re

directory = "app/Filament"

def get_docblock(func_name, indent):
    descriptions = {
        "form": "Konfigurasi form untuk resource ini.",
        "table": "Konfigurasi tabel untuk menampilkan daftar data.",
        "infolist": "Konfigurasi tampilan informasi detail data.",
        "getRelations": "Mengambil daftar relasi (relations) yang terkait dengan resource ini.",
        "getPages": "Mendefinisikan rute dan halaman-halaman yang tersedia untuk resource ini.",
        "getHeaderActions": "Mendapatkan daftar aksi (actions) pada bagian header halaman.",
        "configure": "Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.",
        "getStats": "Mendapatkan daftar widget statistik (Stats) untuk ditampilkan.",
        "getData": "Mendapatkan data statistik untuk ditampilkan pada chart.",
        "getType": "Mendapatkan tipe chart (misal: line, bar, pie, dll).",
        "mount": "Menginisialisasi state awal komponen saat pertama kali dimuat.",
        "getFormSchema": "Mendefinisikan skema form yang digunakan.",
        "getTableColumns": "Mendefinisikan kolom-kolom yang ditampilkan pada tabel.",
        "getNavigationBadge": "Mendapatkan nilai badge yang ditampilkan pada menu navigasi.",
        "getNavigationBadgeColor": "Mendapatkan warna badge pada menu navigasi.",
        "getActions": "Mendapatkan daftar aksi yang tersedia pada halaman ini.",
        "mutateFormDataBeforeCreate": "Memodifikasi data form sebelum disimpan sebagai record baru.",
        "mutateFormDataBeforeSave": "Memodifikasi data form sebelum perubahan disimpan.",
        "getRedirectUrl": "Mendapatkan URL tujuan setelah aksi berhasil dilakukan.",
        "authenticate": "Melakukan proses autentikasi pengguna.",
        "getHeading": "Mendapatkan judul (heading) halaman.",
        "getDescription": "Mendapatkan deskripsi halaman.",
        "getFilters": "Mendapatkan daftar filter yang dapat diterapkan pada data."
    }
    
    desc = descriptions.get(func_name, f"Fungsi {func_name}.")
    
    lines = [
        "/**",
        f" * {desc}",
        " */"
    ]
    return "\n".join(indent + line for line in lines).strip()

modified_files = 0

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith(".php"):
            path = os.path.join(root, file)
            with open(path, "r", encoding="utf-8") as f:
                content = f.read()
            
            pattern = re.compile(r"([ \t]*)(?:public\s+|protected\s+|private\s+)?(?:static\s+)?function\s+([a-zA-Z0-9_]+)\s*\(")
            
            matches = list(pattern.finditer(content))
            if not matches:
                continue
                
            new_content = content
            offset = 0
            
            for match in matches:
                start = match.start() + offset
                indent = match.group(1)
                if not indent:
                    indent = ""
                func_name = match.group(2)
                
                before_str = new_content[:start].rstrip()
                if before_str.endswith("*/"):
                    continue 
                
                docblock = get_docblock(func_name, indent)
                if not indent:
                     insert_text = docblock + "\n"
                else:
                     insert_text = indent + docblock + "\n"
                
                new_content = new_content[:start] + insert_text + new_content[start:]
                offset += len(insert_text)
            
            if new_content != content:
                with open(path, "w", encoding="utf-8") as f:
                    f.write(new_content)
                modified_files += 1
                print(f"Modified {path}")

print(f"Total modified files: {modified_files}")

