<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceLog;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class StafITMaintenanceTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Pemeliharaan Aset Tahun Ini';
    protected static ?int $sort = 3;
    protected string $color = 'info';
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Staf IT');
    }

    protected function getData(): array
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

    protected function getType(): string
    {
        return 'line';
    }
}
