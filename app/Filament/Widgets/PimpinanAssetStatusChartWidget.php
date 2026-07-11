<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PimpinanAssetStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Proporsi Aset Berdasarkan Status'; // heading: Judul yang ditampilkan pada bagian atas widget
    protected static ?int $sort = 2; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Pimpinan');
    }

    /**
     * Mendapatkan data statistik untuk ditampilkan pada chart.
     */
    protected function getData(): array // getData: Mengembalikan susunan data dataset dan label yang akan dirender oleh chart
    {
        $statuses = Asset::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total')) // select: Mengambil kolom tertentu saja dari database untuk dihitung
            ->whereNotNull('status') // whereNotNull: Memfilter data yang tidak bernilai kosong/null
            ->groupBy('status') // groupBy: Mengelompokkan hasil perhitungan berdasarkan kolom tertentu
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $statuses->pluck('total')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
                    'backgroundColor' => [
                        '#10b981', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#3b82f6', // blue
                        '#8b5cf6', // purple
                        '#06b6d4', // cyan
                    ],
                ],
            ],
            'labels' => $statuses->pluck('status')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
        ];
    }

    /**
     * Mendapatkan tipe chart (misal: line, bar, pie, dll).
     */
    protected function getType(): string // getType: Menentukan tipe visualisasi chart (bar, line, pie, doughnut, polarArea)
    {
        return 'doughnut';
    }
}
