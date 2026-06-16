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

        // Menghitung total aset (disimpan di variabel agar bisa digunakan untuk perhitungan persentase)
        $totalAssets = Asset::count();

        // Menghitung jumlah aset kritikal
        $criticalAssets = Asset::where('is_critical', true)->count();

        // Menghitung persentase aset kritikal (dengan pengecekan pembagian nol)
        $criticalPercentage = $totalAssets > 0
            ? round(($criticalAssets / $totalAssets) * 100, 1)
            : 0;

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('Total semua aset terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Total Asset Value', 'Rp ' . number_format($totalCost, 2, ',', '.'))
                ->description('Total biaya pembelian aset')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Critical Assets', $criticalAssets)
                // Menampilkan persentase pada deskripsi stat
                ->description("{$criticalPercentage}% dari total aset")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                // Anda juga bisa membuat warna menjadi dinamis, misalnya jika > 20% maka merah, jika tidak maka kuning
                ->color($criticalPercentage > 20 ? 'danger' : 'warning'),
        ];
    }
}
