<?php

namespace App\Filament\Widgets;

use App\Models\Status;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PimpinanAssetStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Proporsi Aset Berdasarkan Status';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Pimpinan');
    }

    protected function getData(): array
    {
        $statuses = Status::withCount('assets')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $statuses->pluck('assets_count')->toArray(),
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
            'labels' => $statuses->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
