<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PimpinanStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Pimpinan');
    }

    protected function getStats(): array
    {
        $totalAssets = Asset::count();
        $totalAssetValue = Asset::sum('purchase_cost');
        $totalMaintenanceCost = MaintenanceLog::sum('cost');
        $criticalAssets = Asset::where('is_critical', true)->count();

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('Semua aset terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Total Asset Value', 'Rp ' . number_format($totalAssetValue, 2, ',', '.'))
                ->description('Total investasi aset')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Maintenance Cost', 'Rp ' . number_format($totalMaintenanceCost, 2, ',', '.'))
                ->description('Total pengeluaran perawatan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Critical Assets', $criticalAssets)
                ->description('Aset prioritas tinggi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
