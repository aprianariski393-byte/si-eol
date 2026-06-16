<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AssetHealthChart extends ChartWidget
{
    protected ?string $heading = 'Status Kesehatan & Siklus Hidup Aset';

    // Urutan posisi widget di dashboard
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $now = Carbon::now();

        // Batas peringatan: 3 bulan dari sekarang
        $warningDate = Carbon::now()->addMonths(3);

        // 1. Kritis / Habis (EOL sudah lewat)
        $expiredCount = Asset::whereNotNull('eol_date')
            ->whereDate('eol_date', '<', $now)
            ->count();

        // 2. Perhatian / Warning (EOL kurang dari 3 bulan lagi)
        $warningCount = Asset::whereNotNull('eol_date')
            ->whereDate('eol_date', '>=', $now)
            ->whereDate('eol_date', '<=', $warningDate)
            ->count();

        // 3. Aman (EOL masih lama di atas 3 bulan, atau aset tidak punya masa kedaluwarsa)
        $safeCount = Asset::where(function ($query) use ($warningDate) {
            $query->whereDate('eol_date', '>', $warningDate)
                ->orWhereNull('eol_date');
        })->count();

        // Hitung total untuk persentase
        $total = $expiredCount + $warningCount + $safeCount;

        // Cegah pembagian nol dan hitung persentase
        $expiredPct = $total > 0 ? round(($expiredCount / $total) * 100, 1) : 0;
        $warningPct = $total > 0 ? round(($warningCount / $total) * 100, 1) : 0;
        $safePct = $total > 0 ? round(($safeCount / $total) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Total Aset',
                    'data' => [$safeCount, $warningCount, $expiredCount],
                    'backgroundColor' => [
                        '#10b981', // Hijau (Aman - Optimal)
                        '#f59e0b', // Kuning/Amber (Mendekati Batas)
                        '#ef4444', // Merah (Melewati EOL / Harus Diganti)
                    ],
                    // Memberikan efek potongan terpisah saat di-hover
                    'hoverOffset' => 6,
                ],
            ],
            // Menyematkan persentase dan angka langsung pada label
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
