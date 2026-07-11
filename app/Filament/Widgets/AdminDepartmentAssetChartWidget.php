<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminDepartmentAssetChartWidget extends ChartWidget
{
    protected ?string $heading = 'Jumlah Aset per Departemen';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }
    protected function getData(): array
    {
        $departments = Asset::select('department', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $departments->pluck('total')->toArray(),
                    'backgroundColor' => '#3b82f6', // blue
                ],
            ],
            'labels' => $departments->pluck('department')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
