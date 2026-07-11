<?php

$directory = "app/Filament";

function get_docblock($func_name, $indent) {
    $descriptions = [
        "form" => "Konfigurasi form untuk resource ini.",
        "table" => "Konfigurasi tabel untuk menampilkan daftar data.",
        "infolist" => "Konfigurasi tampilan informasi detail data.",
        "getRelations" => "Mengambil daftar relasi (relations) yang terkait dengan resource ini.",
        "getPages" => "Mendefinisikan rute dan halaman-halaman yang tersedia untuk resource ini.",
        "getHeaderActions" => "Mendapatkan daftar aksi (actions) pada bagian header halaman.",
        "configure" => "Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.",
        "getStats" => "Mendapatkan daftar widget statistik (Stats) untuk ditampilkan.",
        "getData" => "Mendapatkan data statistik untuk ditampilkan pada chart.",
        "getType" => "Mendapatkan tipe chart (misal: line, bar, pie, dll).",
        "mount" => "Menginisialisasi state awal komponen saat pertama kali dimuat.",
        "getFormSchema" => "Mendefinisikan skema form yang digunakan.",
        "getTableColumns" => "Mendefinisikan kolom-kolom yang ditampilkan pada tabel.",
        "getNavigationBadge" => "Mendapatkan nilai badge yang ditampilkan pada menu navigasi.",
        "getNavigationBadgeColor" => "Mendapatkan warna badge pada menu navigasi.",
        "getActions" => "Mendapatkan daftar aksi yang tersedia pada halaman ini.",
        "mutateFormDataBeforeCreate" => "Memodifikasi data form sebelum disimpan sebagai record baru.",
        "mutateFormDataBeforeSave" => "Memodifikasi data form sebelum perubahan disimpan.",
        "getRedirectUrl" => "Mendapatkan URL tujuan setelah aksi berhasil dilakukan.",
        "authenticate" => "Melakukan proses autentikasi pengguna.",
        "getHeading" => "Mendapatkan judul (heading) halaman.",
        "getDescription" => "Mendapatkan deskripsi halaman.",
        "getFilters" => "Mendapatkan daftar filter yang dapat diterapkan pada data."
    ];
    
    $desc = isset($descriptions[$func_name]) ? $descriptions[$func_name] : "Fungsi $func_name.";
    
    $lines = [
        "/**",
        " * $desc",
        " */"
    ];
    
    $result = "";
    foreach ($lines as $line) {
        $result .= $indent . $line . "\n";
    }
    
    return rtrim($result);
}

$modified_files = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === "php") {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $pattern = "/([ \t]*)(?:public\s+|protected\s+|private\s+)?(?:static\s+)?function\s+([a-zA-Z0-9_]+)\s*\(/";
        
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $new_content = $content;
            $offset = 0;
            
            for ($i = 0; $i < count($matches[0]); $i++) {
                $start = $matches[0][$i][1] + $offset;
                $indent = $matches[1][$i][0];
                $func_name = $matches[2][$i][0];
                
                $before_str = rtrim(substr($new_content, 0, $start));
                if (str_ends_with($before_str, "*/")) {
                    continue;
                }
                
                $docblock = get_docblock($func_name, $indent);
                $insert_text = $indent . ltrim($docblock) . "\n";
                if ($indent == "") {
                    $insert_text = $docblock . "\n";
                }
                
                $new_content = substr_replace($new_content, $insert_text, $start, 0);
                $offset += strlen($insert_text);
            }
            
            if ($new_content !== $content) {
                file_put_contents($path, $new_content);
                $modified_files++;
                echo "Modified $path\n";
            }
        }
    }
}

echo "Total modified files: $modified_files\n";

?>
