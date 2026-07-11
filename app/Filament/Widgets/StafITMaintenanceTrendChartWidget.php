<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceLog;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class StafITMaintenanceTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Pemeliharaan Aset Tahun Ini'; // heading: Judul yang ditampilkan pada bagian atas widget
    protected static ?int $sort = 3; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard
    protected string $color = 'info';
    protected int|string|array $columnSpan = 1; // columnSpan: Menentukan seberapa lebar widget membentang di dalam layout grid

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Staf IT');
    }

    /**
     * Mendapatkan data statistik untuk ditampilkan pada chart.
     */
    protected function getData(): array // getData: Mengembalikan susunan data dataset dan label yang akan dirender oleh chart
    {
        // Mengelompokkan data berdasarkan tanggal maintenance per bulan tahun ini
        $data = Trend::model(MaintenanceLog::class)
            ->dateColumn('maintenance_date')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Maintenance Logs',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    /**
     * Mendapatkan tipe chart (misal: line, bar, pie, dll).
     */
    protected function getType(): string // getType: Menentukan tipe visualisasi chart (bar, line, pie, doughnut, polarArea)
    {
        return 'line';
    }
}
