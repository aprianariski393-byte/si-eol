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
                // 1. Menggabungkan Nama dan Kode Aset agar hemat kolom
                TextColumn::make('name')
                    ->label('Identitas Aset')
                    ->searchable(['name', 'asset_code']) // Bisa dicari lewat nama atau kode
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn($record) => 'Kode: ' . $record->asset_code)
                    ->wrap(),

                // 2. Menggabungkan Tipe dan Kategori
                TextColumn::make('asset_type')
                    ->label('Tipe & Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Software' => 'info',
                        'Hardware' => 'gray',
                        default => 'gray',
                    })
                    ->description(fn($record) => $record->category?->name)
                    ->searchable(),

                // 3. Status dan Kritis di satu pandangan
                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->searchable(),

                IconColumn::make('is_critical')
                    ->label('Kritis')
                    ->boolean()
                    ->trueIcon(Heroicon::ExclamationTriangle)
                    ->trueColor('danger')
                    ->falseIcon('') // Sembunyikan ikon jika tidak kritis agar tabel bersih
                    ->tooltip('Aset ini sangat penting untuk operasional.'),

                // 4. Menggabungkan Departemen dan Lokasi
                TextColumn::make('department.name')
                    ->label('Pengguna & Lokasi')
                    ->searchable()
                    ->description(fn($record) => $record->location?->name),

                // 5. Fokus Utama Aplikasi: EOL Date dengan Peringatan Warna
                TextColumn::make('eol_date')
                    ->label('End of Life (EOL)')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn($state) =>
                            // Jika EOL lewat atau kurang dari 3 bulan, jadikan merah
                        ($state && \Carbon\Carbon::parse($state) <= now()->addMonths(3)) ? 'danger' : 'success'
                    ),

                // 6. Harga Beli (Format Rupiah)
                TextColumn::make('purchase_cost')
                    ->label('Harga Beli')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignment('right'),

                // ==========================================
                // KOLOM SEKUNDER (Sembunyikan Secara Default)
                // ==========================================
                TextColumn::make('brand')
                    ->label('Merek')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('model_number')
                    ->label('Model')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('serial_number')
                    ->label('Serial/Lisensi')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('purchase_date')
                    ->label('Tgl Beli')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('useful_life_years')
                    ->label('Umur Ekonomis')
                    ->numeric()
                    ->suffix(' Thn')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_subscription')
                    ->label('SaaS/Langganan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subscription_expiry')
                    ->label('Kadaluarsa Langganan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Didaftarkan Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // --- MENAMBAHKAN FILTER DASAR ---
            ->filters([
                SelectFilter::make('asset_type')
                    ->label('Tipe Aset')
                    ->options([
                        'Hardware' => 'Hardware',
                        'Software' => 'Software',
                    ]),

                SelectFilter::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Status Aset')
                    ->preload()
                    ->multiple(), // Bisa filter lebih dari 1 status sekaligus

                TernaryFilter::make('is_critical')
                    ->label('Hanya Aset Kritis'),

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
                        $statusEol = \App\Models\Status::where('name', 'like', '%End of Life%')->first();
                        if ($statusEol) {
                            $oldStatusId = $record->status_id;
                            $record->update(['status_id' => $statusEol->id]);

                            if (class_exists(\App\Models\AssetHistory::class)) {
                                \App\Models\AssetHistory::create([
                                    'asset_id' => $record->id,
                                    'user_id' => Auth::id() ?? 1,
                                    'action' => 'Pensiunkan Aset (Sistem)',
                                    'old_value' => ['status_id' => $oldStatusId],
                                    'new_value' => ['status_id' => $statusEol->id],
                                ]);
                            }
                        }
                    })
                    ->visible(fn($record) => $record->eol_date && \Carbon\Carbon::parse($record->eol_date) <= now()->addMonths(3)),

                Action::make('cetak_detail')
                    ->label('Cetak Detail')
                    ->icon('heroicon-o-printer')
                    ->color('success') // Warna hijau standar Filament
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
            // Menambahkan Default Sorting agar aset yang EOL-nya paling dekat muncul di atas
            ->defaultSort('eol_date', 'asc');
    }
}
