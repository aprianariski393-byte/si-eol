<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Asset;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AdminStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }

    /**
     * Mendapatkan daftar widget statistik (Stats) untuk ditampilkan.
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Total pengguna terdaftar') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-users') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('primary'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Total Roles', Role::count()) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Total peran sistem') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-shield-check') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('info'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Total Departments', Asset::whereNotNull('department')->distinct('department')->count('department')) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Total departemen') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-building-office') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('success'), // color: Menentukan warna utama elemen (seperti success, danger, warning)

            Stat::make('Total Assets', Asset::count()) // Stat: Komponen untuk menampilkan kotak statistik tunggal dengan angka utama
                ->description('Total aset terdaftar') // description: Menambahkan teks penjelasan kecil di bawah angka statistik
                ->descriptionIcon('heroicon-m-cube') // descriptionIcon: Menambahkan ikon kecil di sebelah teks penjelasan
                ->color('warning'), // color: Menentukan warna utama elemen (seperti success, danger, warning)
        ];
    }
}
