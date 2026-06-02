<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class AssetsByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Assets by Category';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Mengambil semua kategori beserta jumlah aset di dalamnya
        $categories = Category::withCount('assets')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $categories->pluck('assets_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // purple
                        '#06b6d4', // cyan
                    ],
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
