<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StafITAssetHealthChartWidget extends ChartWidget
{
    protected ?string $heading = 'Status Kesehatan & Siklus Hidup Aset';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Staf IT');
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $warningDate = Carbon::now()->addMonths(3);

        $expiredCount = Asset::whereNotNull('eol_date')
            ->whereDate('eol_date', '<', $now)
            ->count();

        $warningCount = Asset::whereNotNull('eol_date')
            ->whereDate('eol_date', '>=', $now)
            ->whereDate('eol_date', '<=', $warningDate)
            ->count();

        $safeCount = Asset::where(function ($query) use ($warningDate) {
            $query->whereDate('eol_date', '>', $warningDate)
                ->orWhereNull('eol_date');
        })->count();

        $total = $expiredCount + $warningCount + $safeCount;

        $expiredPct = $total > 0 ? round(($expiredCount / $total) * 100, 1) : 0;
        $warningPct = $total > 0 ? round(($warningCount / $total) * 100, 1) : 0;
        $safePct = $total > 0 ? round(($safeCount / $total) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Total Aset',
                    'data' => [$safeCount, $warningCount, $expiredCount],
                    'backgroundColor' => [
                        '#10b981', // Hijau
                        '#f59e0b', // Kuning
                        '#ef4444', // Merah
                    ],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                "Aman ({$safePct}%)",
                "Mendekati EOL ({$warningPct}%)",
                "Harus Diganti ({$expiredPct}%)"
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
