<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AssetPurchaseTrendChart extends ChartWidget
{
    protected ?string $heading = 'Aset Yang Dibeli Tahun Ini';
    protected static ?int $sort = 3;
    protected string $color = 'info';
    protected int|string|array $columnSpan = 'full';
    public function getDescription(): ?string
    {
        return 'Jumlah aset yang dibeli setiap bulan dalam tahun berjalan.';
    }
    protected function getData(): array
    {
        // Mengelompokkan data berdasarkan tanggal pembelian (purchase_date) per bulan
        $data = Trend::model(Asset::class)
            ->dateColumn('purchase_date')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Assets Purchased',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
