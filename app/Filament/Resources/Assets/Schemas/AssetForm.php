<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // --- KELOMPOK 1: IDENTITAS ASET ---
                Section::make('Identitas Aset')
                    ->description('Informasi dasar dan spesifikasi aset.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextInput::make('asset_code')
                            ->label('Kode Aset')
                            ->placeholder('Contoh: AST-2026-001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('name')
                            ->label('Nama Aset')
                            ->placeholder('Contoh: Server HP ProLiant / Lisensi Windows')
                            ->required()
                            ->maxLength(255),

                        Select::make('asset_type')
                            ->label('Tipe Aset')
                            ->options([
                                'Hardware' => 'Hardware (Perangkat Keras)',
                                'Software' => 'Software (Perangkat Lunak)'
                            ])
                            ->default('Hardware')
                            ->live() // <-- Memicu perubahan UI saat diganti
                            ->required(),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->searchable()
                            ->preload(),

                        Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->label('Vendor / Pemasok')
                            ->searchable()
                            ->preload(),

                        TextInput::make('brand')
                            ->label('Merek')
                            ->placeholder('Contoh: Lenovo, Microsoft'),

                        TextInput::make('model_number')
                            ->label('Model / Versi')
                            ->placeholder('Contoh: T14 Gen 3 / v2024'),

                        TextInput::make('serial_number')
                            ->label('Serial Number / Lisensi Key')
                            ->placeholder('SN atau Key Unik')
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(3) // Dibagi menjadi 3 kolom agar hemat tempat
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 2: KHUSUS SOFTWARE (DINAMIS) ---
                Section::make('Detail Lisensi Software')
                    ->description('Atur masa berlaku langganan perangkat lunak.')
                    ->icon(Heroicon::OutlinedComputerDesktop)
                    ->schema([
                        Toggle::make('is_subscription')
                            ->label('Apakah Berlangganan (SaaS)?')
                            ->live()
                            ->inline(false),

                        DatePicker::make('subscription_expiry')
                            ->label('Tanggal Kadaluarsa Langganan')
                            ->native(false) // Menggunakan kalender pop-up Filament (bukan bawaan browser)
                            ->visible(fn(Get $get) => $get('is_subscription')) // Hanya muncul jika Toggle aktif
                            ->required(fn(Get $get) => $get('is_subscription')),
                    ])
                    ->columns(2)
                    ->visible(fn(Get $get) => $get('asset_type') === 'Software') // SEKSI INI HILANG JIKA HARDWARE
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 3: FINANSIAL & LIFECYCLE ---
                Section::make('Finansial & Siklus Hidup (Lifecycle)')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->schema([
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->native(false)
                            ->displayFormat('d M Y'),

                        TextInput::make('purchase_cost')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp') // Disesuaikan untuk Indonesia
                            ->maxValue(999999999999.99),

                        TextInput::make('useful_life_years')
                            ->label('Umur Ekonomis')
                            ->numeric()
                            ->suffix('Tahun'),

                        DatePicker::make('eol_date')
                            ->label('Tanggal EOL (End of Life)')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->helperText('Perkiraan aset harus diganti atau dukungan berakhir.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 4: OPERASIONAL & STATUS ---
                Section::make('Lokasi & Status Operasional')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        Select::make('status_id')
                            ->relationship('status', 'name')
                            ->label('Status Aset')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('location_id')
                            ->relationship('location', 'name')
                            ->label('Lokasi')
                            ->searchable()
                            ->preload(),

                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->label('Departemen Pengguna')
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_critical')
                            ->label('Aset Kritis (Critical Asset)?')
                            ->helperText('Tandai jika kerusakan aset ini mengganggu operasional pabrik.')
                            ->onColor('danger') // Warna merah jika diaktifkan
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // --- KELOMPOK 5: CATATAN ---
                Section::make('Catatan Tambahan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi Aset')
                            ->hiddenLabel() // Sembunyikan label karena nama Section sudah jelas
                            ->placeholder('Tambahkan catatan spesifik mengenai aset ini...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }
}
