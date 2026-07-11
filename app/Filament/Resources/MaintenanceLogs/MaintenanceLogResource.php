<?php

namespace App\Filament\Resources\MaintenanceLogs;

use App\Filament\Resources\MaintenanceLogs\Pages\ManageMaintenanceLogs;
use App\Models\MaintenanceLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MaintenanceLogResource extends Resource
{
    protected static ?string $model = MaintenanceLog::class;

    // Menggunakan ikon Kunci Pas dan Obeng (Mewakili Perbaikan/Maintenance)
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    // Masukkan ke grup Asset Management
    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    // Ubah judul pencarian ke 'maintenance_type' karena log tidak punya 'name'
    protected static ?string $recordTitleAttribute = 'maintenance_type';
    protected static ?string $modelLabel = 'Catatan Pemeliharaan';
    protected static ?string $pluralModelLabel = 'Riwayat Pemeliharaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // KELOMPOK 1: Detail Pelaksanaan
                Section::make('Informasi Pelaksanaan')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->schema([
                        Select::make('asset_id')
                            ->relationship('asset', 'name')
                            ->label('Aset Terkait')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-cube')
                            ->placeholder('Pilih Aset...')
                            ->columnSpanFull(),

                        DatePicker::make('maintenance_date')
                            ->label('Tanggal Pemeliharaan')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->required()
                            ->prefixIcon('heroicon-m-calendar')
                            ->default(now()), // Default ke hari ini

                        TextInput::make('maintenance_type')
                            ->label('Jenis Pemeliharaan')
                            ->placeholder('Contoh: Patching / Upgrade RAM / Ganti Oli')
                            ->maxLength(100)
                            ->prefixIcon('heroicon-m-wrench')
                            ->required(),

                        TextInput::make('performed_by')
                            ->label('Dilakukan Oleh (Teknisi/Vendor)')
                            ->placeholder('Nama internal atau pihak ketiga')
                            ->prefixIcon('heroicon-m-user-circle')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // KELOMPOK 2: Biaya & Penjadwalan Lanjutan
                Section::make('Biaya & Tindak Lanjut')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->schema([
                        TextInput::make('cost')
                            ->label('Biaya Pemeliharaan')
                            ->numeric()
                            ->prefix('Rp')
                            ->prefixIcon('heroicon-m-banknotes')
                            ->placeholder('0')
                            ->maxValue(999999999999.99),

                        DatePicker::make('next_maintenance_date')
                            ->label('Jadwal Pemeliharaan Berikutnya')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->placeholder('Pilih Tanggal...')
                            ->helperText('Kosongkan jika tidak ada jadwal rutin.'),

                        Textarea::make('description')
                            ->label('Catatan Hasil / Deskripsi Masalah')
                            ->placeholder('Jelaskan apa saja yang diperbaiki atau diganti...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Catatan Pemeliharaan')
                    ->schema([
                        TextEntry::make('asset.name')
                            ->label('Nama Aset')
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('maintenance_date')
                            ->label('Tanggal Pelaksanaan')
                            ->date('d F Y')
                            ->placeholder('-'),

                        TextEntry::make('maintenance_type')
                            ->label('Jenis Pemeliharaan')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),

                        TextEntry::make('performed_by')
                            ->label('Teknisi')
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('Tidak diketahui'),

                        TextEntry::make('cost')
                            ->label('Total Biaya')
                            ->money('IDR', locale: 'id')
                            ->weight('semibold')
                            ->placeholder('Rp 0,00'),

                        TextEntry::make('next_maintenance_date')
                            ->label('Jadwal Berikutnya')
                            ->date('d M Y')
                            ->badge()
                            ->color(
                                fn($state) =>
                                // Beri warna merah jika jadwal berikutnya sudah terlewat
                                ($state && \Carbon\Carbon::parse($state) < now()) ? 'danger' : 'success'
                            )
                            ->placeholder('Tidak terjadwal'),

                        TextEntry::make('description')
                            ->label('Deskripsi Pekerjaan')
                            ->placeholder('Tidak ada catatan spesifik.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('maintenance_type')
            ->columns([
                TextColumn::make('maintenance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->searchable()
                    // Menggabungkan jenis pemeliharaan di bawah nama aset agar hemat kolom
                    ->description(fn(MaintenanceLog $record) => 'Jenis: ' . ($record->maintenance_type ?? '-')),

                TextColumn::make('performed_by')
                    ->label('Teknisi/Vendor')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignment('right'),

                TextColumn::make('next_maintenance_date')
                    ->label('Jadwal Berikutnya')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn($state) => ($state && \Carbon\Carbon::parse($state) < now()) ? 'danger' : 'gray'
                    ),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            // Logika penting: Selalu tampilkan perbaikan terbaru di paling atas tabel
            ->defaultSort('maintenance_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            // Tetap menggunakan Modal Pop-up (Manage) karena form-nya cukup ringkas
            'index' => ManageMaintenanceLogs::route('/'),
        ];
    }
}
