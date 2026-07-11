<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StafITStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Staf IT');
    }

    protected function getStats(): array
    {
        $now = Carbon::now();

        $totalAssets = Asset::count();
        
        // Kita bisa ambil dari MaintenanceLog yang belum selesai atau status aset.
        // Asumsi: status_id untuk maintenance adalah misal relasinya, tapi kita cek Asset dengan relasi MaintenanceLog.
        // Untuk sederhananya kita asumsikan status "Maintenance" bisa di cek di status_id atau kita hitung total logs.
        $totalMaintenanceLogs = MaintenanceLog::count();
        
        // Menghitung aset dengan eol_date sudah lewat (Expired/Warning)
        $expiredAssets = Asset::whereNotNull('eol_date')
            ->whereDate('eol_date', '<=', $now)
            ->count();

        // Aset yang sedang dalam perbaikan (Maintenance)
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        return [
            Stat::make('Total Aset', $totalAssets)
                ->description('Semua aset')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Dalam Perbaikan', $maintenanceAssets)
                ->description('Aset sedang maintenance')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('warning'),

            Stat::make('Total Log Pemeliharaan', $totalMaintenanceLogs)
                ->description('Riwayat pemeliharaan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),

            Stat::make('Aset Pensiun (EOL)', $expiredAssets)
                ->description('Aset melewati batas pakai')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
