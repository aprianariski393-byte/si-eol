<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminUserRoleChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Pengguna berdasarkan Peran';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }

    protected function getData(): array
    {
        $roles = Role::withCount('users')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Users',
                    'data' => $roles->pluck('users_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // purple
                    ],
                ],
            ],
            'labels' => $roles->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
