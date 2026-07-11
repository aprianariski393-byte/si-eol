<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
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
        $statuses = Asset::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('status')
            ->groupBy('status')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $statuses->pluck('total')->toArray(),
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
            'labels' => $statuses->pluck('status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
