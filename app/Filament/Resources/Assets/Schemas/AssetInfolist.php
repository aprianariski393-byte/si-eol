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

                // --- KELOMPOK 1: IDENTITAS ASET ---
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
                            ->copyable() // <-- Fitur klik untuk salin (UX yang bagus)
                            ->copyMessage('Kode Aset disalin!')
                            ->icon(Heroicon::OutlinedDocumentDuplicate),

                        TextEntry::make('asset_type')
                            ->label('Tipe Aset')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Software' => 'info',
                                'Hardware' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->placeholder('-'),

                        TextEntry::make('brand')
                            ->label('Merek')
                            ->placeholder('-'),

                        TextEntry::make('model_number')
                            ->label('Model/Versi')
                            ->placeholder('-'),

                        TextEntry::make('serial_number')
                            ->label('Serial Number / Lisensi')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('vendor.name')
                            ->label('Vendor (Pemasok)')
                            ->icon(Heroicon::OutlinedBuildingStorefront)
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 2: KHUSUS SOFTWARE (DINAMIS) ---
                Section::make('Detail Lisensi Software')
                    ->icon(Heroicon::OutlinedComputerDesktop)
                    ->schema([
                        IconEntry::make('is_subscription')
                            ->label('Status Berlangganan')
                            ->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)
                            ->falseIcon(Heroicon::OutlinedXCircle),

                        TextEntry::make('subscription_expiry')
                            ->label('Tanggal Kadaluarsa')
                            ->date('d F Y')
                            ->color(
                                fn($record) => ($record->subscription_expiry && $record->subscription_expiry < now()) ? 'danger' : 'success'
                            )
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    // Hanya tampilkan seksi ini jika tipe asetnya Software
                    ->visible(fn($record) => $record?->asset_type === 'Software')
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 3: FINANSIAL & LIFECYCLE ---
                Section::make('Finansial & Siklus Hidup')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->schema([
                        TextEntry::make('purchase_cost')
                            ->label('Harga Beli')
                            ->money('IDR', locale: 'id') // Format Rupiah Indonesia
                            ->weight('semibold')
                            ->placeholder('-'),

                        TextEntry::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('useful_life_years')
                            ->label('Umur Ekonomis')
                            ->numeric()
                            ->suffix(' Tahun')
                            ->placeholder('-'),

                        TextEntry::make('eol_date')
                            ->label('End of Life (EOL)')
                            ->date('d M Y')
                            ->badge()
                            ->color(
                                fn($record) => ($record->eol_date && $record->eol_date <= now()->addMonths(3)) ? 'danger' : 'success'
                            )
                            ->placeholder('-'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 4: OPERASIONAL & STATUS ---
                Section::make('Operasional')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->badge()
                            // Asumsi Anda akan menggunakan kolom 'color' dari tabel statuses nanti
                            // ->color(fn ($record) => $record->status?->color ?? 'gray')
                            ->placeholder('-'),

                        TextEntry::make('location.name')
                            ->label('Lokasi')
                            ->placeholder('-'),

                        TextEntry::make('department.name')
                            ->label('Departemen')
                            ->placeholder('-'),

                        IconEntry::make('is_critical')
                            ->label('Aset Kritis')
                            ->boolean()
                            ->trueIcon(Heroicon::ExclamationTriangle)
                            ->trueColor('danger') // Warna merah menyala jika aset ini kritis
                            ->falseColor('gray'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 5: CATATAN & SISTEM ---
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
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }
}
