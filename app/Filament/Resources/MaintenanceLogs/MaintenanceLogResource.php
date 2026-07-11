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

    /**
     * Konfigurasi form untuk resource ini.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // KELOMPOK 1: Detail Pelaksanaan
                Section::make('Informasi Pelaksanaan') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->schema([
                        Select::make('asset_id') // Select: Komponen dropdown untuk memilih opsi
                            ->relationship('asset', 'name') // relationship: Mengambil data dari relasi model
                            ->label('Aset Terkait') // label: Teks label yang ditampilkan untuk komponen ini
                            ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                            ->preload() // preload: Memuat data terlebih dahulu sebelum pencarian
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->prefixIcon('heroicon-m-cube') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->placeholder('Pilih Aset...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                        DatePicker::make('maintenance_date') // DatePicker: Komponen input tanggal
                            ->label('Tanggal Pemeliharaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->displayFormat('d M Y') // displayFormat: Format tampilan (misal format penulisan tanggal)
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->prefixIcon('heroicon-m-calendar') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->default(now()), // Default ke hari ini

                        TextInput::make('maintenance_type') // TextInput: Komponen input teks biasa
                            ->label('Jenis Pemeliharaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Contoh: Patching / Upgrade RAM / Ganti Oli') // placeholder: Teks abu-abu panduan saat input kosong
                            ->maxLength(100) // maxLength: Batas maksimal jumlah karakter
                            ->prefixIcon('heroicon-m-wrench') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->required(), // required: Menandakan bahwa field ini wajib diisi

                        TextInput::make('performed_by') // TextInput: Komponen input teks biasa
                            ->label('Dilakukan Oleh (Teknisi/Vendor)') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Nama internal atau pihak ketiga') // placeholder: Teks abu-abu panduan saat input kosong
                            ->prefixIcon('heroicon-m-user-circle') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->maxLength(255), // maxLength: Batas maksimal jumlah karakter
                    ])
                    ->columns(3) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4

                // KELOMPOK 2: Biaya & Penjadwalan Lanjutan
                Section::make('Biaya & Tindak Lanjut') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->schema([
                        TextInput::make('cost') // TextInput: Komponen input teks biasa
                            ->label('Biaya Pemeliharaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->numeric() // numeric: Memastikan input/kolom berupa angka
                            ->prefix('Rp')
                            ->prefixIcon('heroicon-m-banknotes') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->placeholder('0') // placeholder: Teks abu-abu panduan saat input kosong
                            ->maxValue(999999999999.99),

                        DatePicker::make('next_maintenance_date') // DatePicker: Komponen input tanggal
                            ->label('Jadwal Pemeliharaan Berikutnya') // label: Teks label yang ditampilkan untuk komponen ini
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->displayFormat('d M Y') // displayFormat: Format tampilan (misal format penulisan tanggal)
                            ->prefixIcon('heroicon-m-calendar-days') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->placeholder('Pilih Tanggal...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->helperText('Kosongkan jika tidak ada jadwal rutin.'), // helperText: Teks bantuan kecil di bawah komponen

                        Textarea::make('description') // Textarea: Komponen input teks panjang
                            ->label('Catatan Hasil / Deskripsi Masalah') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Jelaskan apa saja yang diperbaiki atau diganti...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->rows(3)
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
                    ])
                    ->columns(2) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    /**
     * Konfigurasi tampilan informasi detail data.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Catatan Pemeliharaan') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->schema([
                        TextEntry::make('asset.name') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Nama Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                        TextEntry::make('maintenance_date') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Tanggal Pelaksanaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->date('d F Y') // date: Format data sebagai tanggal saja
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('maintenance_type') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Jenis Pemeliharaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->badge() // badge: Menampilkan item dengan gaya badge warna
                            ->color('info')
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('performed_by') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Teknisi') // label: Teks label yang ditampilkan untuk komponen ini
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('Tidak diketahui'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('cost') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Total Biaya') // label: Teks label yang ditampilkan untuk komponen ini
                            ->money('IDR', locale: 'id')
                            ->weight('semibold')
                            ->placeholder('Rp 0,00'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('next_maintenance_date') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Jadwal Berikutnya') // label: Teks label yang ditampilkan untuk komponen ini
                            ->date('d M Y') // date: Format data sebagai tanggal saja
                            ->badge() // badge: Menampilkan item dengan gaya badge warna
                            ->color(
                                fn($state) =>
                                // Beri warna merah jika jadwal berikutnya sudah terlewat
                                ($state && \Carbon\Carbon::parse($state) < now()) ? 'danger' : 'success'
                            )
                            ->placeholder('Tidak terjadwal'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('description') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Deskripsi Pekerjaan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Tidak ada catatan spesifik.') // placeholder: Teks abu-abu panduan saat input kosong
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
                    ])
                    ->columns(3) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    /**
     * Konfigurasi tabel untuk menampilkan daftar data.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('maintenance_type')
            ->columns([ // columns: Menentukan jumlah grid/kolom
                TextColumn::make('maintenance_date') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Tanggal') // label: Teks label yang ditampilkan untuk komponen ini
                    ->date('d M Y') // date: Format data sebagai tanggal saja
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->weight('bold'),

                TextColumn::make('asset.name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Aset') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    // Menggabungkan jenis pemeliharaan di bawah nama aset agar hemat kolom
                    ->description(fn(MaintenanceLog $record) => 'Jenis: ' . ($record->maintenance_type ?? '-')),

                TextColumn::make('performed_by') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Teknisi/Vendor') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                TextColumn::make('cost') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Biaya') // label: Teks label yang ditampilkan untuk komponen ini
                    ->money('IDR', locale: 'id')
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->alignment('right'),

                TextColumn::make('next_maintenance_date') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Jadwal Berikutnya') // label: Teks label yang ditampilkan untuk komponen ini
                    ->date('d M Y') // date: Format data sebagai tanggal saja
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
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
                BulkActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    DeleteBulkAction::make(),
                ]),
            ])
            // Logika penting: Selalu tampilkan perbaikan terbaru di paling atas tabel
            ->defaultSort('maintenance_date', 'desc');
    }

    /**
     * Mendefinisikan rute dan halaman-halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            // Tetap menggunakan Modal Pop-up (Manage) karena form-nya cukup ringkas
            'index' => ManageMaintenanceLogs::route('/'),
        ];
    }
}
