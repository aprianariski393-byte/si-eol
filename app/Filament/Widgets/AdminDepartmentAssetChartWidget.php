<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminDepartmentAssetChartWidget extends ChartWidget
{
    protected ?string $heading = 'Jumlah Aset per Departemen'; // heading: Judul yang ditampilkan pada bagian atas widget
    protected static ?int $sort = 3; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard
    protected int|string|array $columnSpan = 1; // columnSpan: Menentukan seberapa lebar widget membentang di dalam layout grid

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }
    /**
     * Mendapatkan data statistik untuk ditampilkan pada chart.
     */
    protected function getData(): array // getData: Mengembalikan susunan data dataset dan label yang akan dirender oleh chart
    {
        $departments = Asset::select('department', \Illuminate\Support\Facades\DB::raw('count(*) as total')) // select: Mengambil kolom tertentu saja dari database untuk dihitung
            ->whereNotNull('department') // whereNotNull: Memfilter data yang tidak bernilai kosong/null
            ->groupBy('department') // groupBy: Mengelompokkan hasil perhitungan berdasarkan kolom tertentu
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $departments->pluck('total')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
                    'backgroundColor' => '#3b82f6', // blue
                ],
            ],
            'labels' => $departments->pluck('department')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
        ];
    }

    /**
     * Mendapatkan tipe chart (misal: line, bar, pie, dll).
     */
    protected function getType(): string // getType: Menentukan tipe visualisasi chart (bar, line, pie, doughnut, polarArea)
    {
        return 'bar';
    }
}
