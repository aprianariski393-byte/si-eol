<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class PimpinanPurchaseTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Pembelian Aset Tahun Ini'; // heading: Judul yang ditampilkan pada bagian atas widget
    protected static ?int $sort = 3; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard
    protected string $color = 'info';
    protected int|string|array $columnSpan = 1; // columnSpan: Menentukan seberapa lebar widget membentang di dalam layout grid

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
        $data = Trend::model(Asset::class)
            ->dateColumn('purchase_date')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Assets Purchased',
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
