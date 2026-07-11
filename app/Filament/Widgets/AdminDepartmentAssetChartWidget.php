<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminDepartmentAssetChartWidget extends ChartWidget
{
    protected ?string $heading = 'Jumlah Aset per Departemen';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Administrator');
    }

    protected function getData(): array
    {
        // Asumsi relasi di Department model adalah `assets()`
        // Jika belum ada withCount('assets') pastikan relasi itu ada. 
        // Jika model Department belum memiliki relasi assets(), ini mungkin akan gagal, 
        // tapi kita bisa query lewat Asset::select raw juga jika diperlukan.
        // Kita asumsikan model Department -> hasMany(Asset::class)
        $departments = Department::withCount('assets')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $departments->pluck('assets_count')->toArray(),
                    'backgroundColor' => '#3b82f6', // blue
                ],
            ],
            'labels' => $departments->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
