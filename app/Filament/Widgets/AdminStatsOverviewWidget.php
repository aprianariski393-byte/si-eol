<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Department;
use App\Models\Location;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AdminStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Roles', Role::count())
                ->description('Total peran sistem')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info'),

            Stat::make('Total Departments', Department::count())
                ->description('Total departemen')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Total Locations', Location::count())
                ->description('Total lokasi fisik')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
        ];
    }
}
