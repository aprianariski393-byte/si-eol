<?php

$directory = "app/Filament/Widgets";

$comments = [
    "protected ?string \$heading" => "heading: Judul yang ditampilkan pada bagian atas widget",
    "protected static ?int \$sort" => "sort: Urutan prioritas widget saat ditampilkan di halaman dashboard",
    "protected int|string|array \$columnSpan" => "columnSpan: Menentukan seberapa lebar widget membentang di dalam layout grid",
    "public static function canView" => "canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini",
    "Stat::make(" => "Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama",
    "->description(" => "description: Menambahkan teks penjelasan kecil di bawah angka statistik",
    "->descriptionIcon(" => "descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan",
    "->color(" => "color: Menentukan warna utama elemen (seperti success, danger, warning)",
    "->chart(" => "chart: Menambahkan grafik mini sparkline di latar belakang kotak statistik",
    "getData(): array" => "getData: Mengembalikan susunan data dataset dan label yang akan dirender oleh chart",
    "getType(): string" => "getType: Menentukan tipe visualisasi chart (bar, line, pie, doughnut, polarArea)",
    "Asset::select(" => "select: Mengambil kolom tertentu saja dari database untuk dihitung",
    "->whereNotNull(" => "whereNotNull: Memfilter data yang tidak bernilai kosong/null",
    "->groupBy(" => "groupBy: Mengelompokkan hasil perhitungan berdasarkan kolom tertentu",
    "->pluck(" => "pluck: Mengambil array dari satu kolom spesifik pada hasil query"
];

$modified_files = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === "php") {
        $path = $file->getPathname();
        
        $lines = file($path);
        $new_lines = [];
        $modified = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            if (strpos($trimmed, "//") === false && strpos($trimmed, "/*") === false && $trimmed !== "") {
                foreach ($comments as $key => $comment) {
                    if (strpos($line, $key) !== false) {
                        $line = rtrim($line) . " // " . $comment . "\n";
                        $modified = true;
                        break;
                    }
                }
            }
            $new_lines[] = $line;
        }
        
        if ($modified) {
            file_put_contents($path, implode("", $new_lines));
            $modified_files++;
            echo "Modified $path\n";
        }
    }
}

echo "Total modified files: $modified_files\n";
?>
