<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PimpinanStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Pimpinan');
    }

    /**
     * Mendapatkan daftar widget statistik (Stats) untuk ditampilkan.
     */
    protected function getStats(): array
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'Active')->count();
        $totalMaintenanceCost = MaintenanceLog::sum('cost');
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        return [
            Stat::make('Total Aset', $totalAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Semua aset terdaftar') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-cube') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('primary'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Aset Aktif', $activeAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Aset yang sedang digunakan') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-check-badge') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('success'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Total Biaya Perawatan', 'Rp ' . number_format($totalMaintenanceCost, 2, ',', '.')) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Total pengeluaran perawatan') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-banknotes') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('warning'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Dalam Perbaikan', $maintenanceAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Aset sedang dalam perbaikan') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-wrench') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('danger'), // color: Menentukan warna utama elemen (seperti success, danger, warning)
        ];
    }
}
