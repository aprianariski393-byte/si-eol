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
    protected static ?int $sort = 1; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Staf IT');
    }

    /**
     * Mendapatkan daftar widget statistik (Stats) untuk ditampilkan.
     */
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
            Stat::make('Total Aset', $totalAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Semua aset') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-cube') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('primary'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Dalam Perbaikan', $maintenanceAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Aset sedang maintenance') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-wrench') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('warning'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Total Log Pemeliharaan', $totalMaintenanceLogs) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Riwayat pemeliharaan') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-wrench-screwdriver') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('info'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Aset Pensiun (EOL)', $expiredAssets) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Aset melewati batas pakai') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-x-circle') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('danger'), // color: Menentukan warna utama elemen (seperti success, danger, warning)
        ];
    }
}
