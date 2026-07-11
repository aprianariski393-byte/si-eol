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
        $activeAssets = Asset::where('status', 'Active')->count();
        $totalMaintenanceCost = MaintenanceLog::sum('cost');
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        return [
            Stat::make('Total Aset', $totalAssets)
                ->description('Semua aset terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Aset Aktif', $activeAssets)
                ->description('Aset yang sedang digunakan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Biaya Perawatan', 'Rp ' . number_format($totalMaintenanceCost, 2, ',', '.'))
                ->description('Total pengeluaran perawatan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Dalam Perbaikan', $maintenanceAssets)
                ->description('Aset sedang dalam perbaikan')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('danger'),
        ];
    }
}
