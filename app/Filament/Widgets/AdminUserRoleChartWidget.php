<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminUserRoleChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Pengguna berdasarkan Peran'; // heading: Judul yang ditampilkan pada bagian atas widget
    protected static ?int $sort = 2; // sort: Urutan prioritas widget saat ditampilkan di halaman dashboard

    /**
     * Fungsi canView.
     */
    public static function canView(): bool // canView: Menentukan apakah user yang login memiliki akses untuk melihat widget ini
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }

    /**
     * Mendapatkan data statistik untuk ditampilkan pada chart.
     */
    protected function getData(): array // getData: Mengembalikan susunan data dataset dan label yang akan dirender oleh chart
    {
        $roles = Role::withCount('users')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Users',
                    'data' => $roles->pluck('users_count')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // purple
                    ],
                ],
            ],
            'labels' => $roles->pluck('name')->toArray(), // pluck: Mengambil array dari satu kolom spesifik pada hasil query
        ];
    }

    /**
     * Mendapatkan tipe chart (misal: line, bar, pie, dll).
     */
    protected function getType(): string // getType: Menentukan tipe visualisasi chart (bar, line, pie, doughnut, polarArea)
    {
        return 'doughnut';
    }
}
