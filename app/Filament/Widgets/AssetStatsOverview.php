<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Menghitung total nilai aset
        $totalCost = Asset::sum('purchase_cost');

        return [
            Stat::make('Total Assets', Asset::count())
                ->description('Total semua aset terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Total Asset Value', 'Rp ' . number_format($totalCost, 2, ',', '.'))
                ->description('Total biaya pembelian aset')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Critical Assets', Asset::where('is_critical', true)->count())
                ->description('Aset prioritas tinggi / kritikal')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
