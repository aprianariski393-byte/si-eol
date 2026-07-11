<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Identitas Aset')
                    ->searchable(['name', 'asset_code'])
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn($record) => 'Kode: ' . $record->asset_code)
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'IT Equipment' => 'Peralatan IT',
                        'Software' => 'Perangkat Lunak',
                        'Furniture' => 'Mebel',
                        'Vehicles' => 'Kendaraan',
                        'Machinery' => 'Mesin',
                        default => $state,
                    })
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Active' => 'Aktif',
                        'Maintenance' => 'Dalam Perbaikan',
                        'End of Life' => 'Pensiun (EOL)',
                        'Disposed' => 'Dihapus',
                        'Lost' => 'Hilang',
                        default => $state,
                    })
                    ->searchable(),

                TextColumn::make('department')
                    ->label('Departemen Pengguna')
                    ->searchable(),

                TextColumn::make('eol_date')
                    ->label('End of Life (EOL)')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn($state) =>
                        ($state && \Carbon\Carbon::parse($state) <= now()->addMonths(3)) ? 'danger' : 'success'
                    ),

                TextColumn::make('brand')
                    ->label('Merek')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('serial_number')
                    ->label('Serial/Lisensi')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('purchase_date')
                    ->label('Tgl Beli')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Didaftarkan Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'IT Equipment' => 'Peralatan IT',
                        'Software' => 'Perangkat Lunak',
                        'Furniture' => 'Mebel',
                        'Vehicles' => 'Kendaraan',
                        'Machinery' => 'Mesin',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Aset')
                    ->options([
                        'Active' => 'Aktif',
                        'Maintenance' => 'Dalam Perbaikan',
                        'End of Life' => 'Pensiun (EOL)',
                        'Disposed' => 'Dihapus',
                        'Lost' => 'Hilang',
                    ])
                    ->multiple(),

                \Filament\Tables\Filters\Filter::make('eol_status')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'aman' => 'Aman (> 3 Bulan)',
                                'warning' => 'Mendekati EOL (≤ 3 Bulan)',
                                'kritis' => 'Kritis / Sudah EOL',
                            ])
                            ->label('Status EOL')
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['status'],
                            function (\Illuminate\Database\Eloquent\Builder $query, $status) {
                                $now = now();
                                $warningDate = now()->addMonths(3);
                                if ($status === 'aman') {
                                    return $query->where(function ($q) use ($warningDate) {
                                        $q->whereDate('eol_date', '>', $warningDate)
                                            ->orWhereNull('eol_date');
                                    });
                                } elseif ($status === 'warning') {
                                    return $query->whereDate('eol_date', '>=', $now)
                                        ->whereDate('eol_date', '<=', $warningDate);
                                } elseif ($status === 'kritis') {
                                    return $query->whereDate('eol_date', '<', $now);
                                }
                                return $query;
                            }
                        );
                    }),
            ])
            ->recordActions([
                Action::make('dispose')
                    ->label('Pensiunkan (EOL)')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Pensiunkan Aset EOL')
                    ->modalDescription('Apakah Anda yakin ingin mengubah status aset ini menjadi End of Life (Pensiun)?')
                    ->action(function ($record) {
                        $record->update(['status' => 'End of Life']);
                    })
                    ->visible(fn($record) => $record->eol_date && \Carbon\Carbon::parse($record->eol_date) <= now()->addMonths(3) && !in_array($record->status, ['End of Life', 'Disposed'])),

                Action::make('cetak_detail')
                    ->label('Cetak Detail')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('asset.cetakDetailPdf', $record->id))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('eol_date', 'asc');
    }
}
