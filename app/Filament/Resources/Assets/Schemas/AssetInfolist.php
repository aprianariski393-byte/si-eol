<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Aset')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Aset')
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('asset_code')
                            ->label('Kode Aset')
                            ->copyable()
                            ->copyMessage('Kode Aset disalin!')
                            ->icon(Heroicon::OutlinedDocumentDuplicate),

                        TextEntry::make('category')
                            ->label('Kategori')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'IT Equipment' => 'Peralatan IT',
                                'Software' => 'Perangkat Lunak',
                                'Furniture' => 'Mebel',
                                'Vehicles' => 'Kendaraan',
                                'Machinery' => 'Mesin',
                                default => $state,
                            })
                            ->placeholder('-'),

                        TextEntry::make('brand')
                            ->label('Merek')
                            ->placeholder('-'),

                        TextEntry::make('serial_number')
                            ->label('Serial Number / Lisensi')
                            ->copyable()
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Siklus Hidup (End of Life)')
                    ->icon(Heroicon::OutlinedClock)
                    ->schema([
                        TextEntry::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('eol_date')
                            ->label('End of Life (EOL)')
                            ->date('d M Y')
                            ->badge()
                            ->color(
                                fn($record) => ($record->eol_date && \Carbon\Carbon::parse($record->eol_date) <= now()->addMonths(3)) ? 'danger' : 'success'
                            )
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Operasional')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        TextEntry::make('status')
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
                            ->placeholder('-'),

                        TextEntry::make('department')
                            ->label('Departemen')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Deskripsi/Catatan')
                            ->placeholder('Tidak ada catatan.')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Didaftarkan Pada')
                            ->dateTime('d M Y, H:i')
                            ->color('gray'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->color('gray'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
