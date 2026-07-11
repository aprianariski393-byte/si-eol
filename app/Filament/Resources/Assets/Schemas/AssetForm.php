<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class AssetForm
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Utama') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->schema([
                        TextInput::make('asset_code') // TextInput: Komponen input teks biasa
                            ->label('Kode Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->default(function () { // default: Nilai bawaan (awal) jika tidak ada input
                                $prefix = 'AST-' . date('Y') . '-';
                                $lastAsset = Asset::where('asset_code', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                                if (!$lastAsset)
                                    return $prefix . '0001';
                                $parts = explode('-', $lastAsset->asset_code);
                                return $prefix . str_pad(((int) end($parts)) + 1, 4, '0', STR_PAD_LEFT);
                            })
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->unique(ignoreRecord: true) // unique: Memastikan nilai unik di dalam database
                            ->readOnly() // readOnly: Field hanya bisa dibaca, tidak bisa diubah
                            ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ->dehydrated() // dehydrated: Menentukan apakah data akan dikirim/disimpan ke database
                            ->maxLength(50) // maxLength: Batas maksimal jumlah karakter
                            ->prefixIcon('heroicon-m-qr-code') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->helperText('Kode unik yang dihasilkan secara otomatis oleh sistem.'), // helperText: Teks bantuan kecil di bawah komponen

                        TextInput::make('name') // TextInput: Komponen input teks biasa
                            ->label('Nama Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Contoh: Laptop Asus ROG / Meja Kerja') // placeholder: Teks abu-abu panduan saat input kosong
                            ->prefixIcon('heroicon-m-cube') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->maxLength(255), // maxLength: Batas maksimal jumlah karakter

                        Select::make('category') // Select: Komponen dropdown untuk memilih opsi
                            ->label('Kategori') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Pilih Kategori Aset...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                                'IT Equipment' => 'Peralatan IT',
                                'Software' => 'Perangkat Lunak',
                                'Furniture' => 'Mebel',
                                'Vehicles' => 'Kendaraan',
                                'Machinery' => 'Mesin',
                            ])
                            ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->prefixIcon('heroicon-m-tag') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->live() // live: Merespon perubahan input secara real-time ke server
                            ->afterStateUpdated(function (Get $get, Set $set, $state) { // afterStateUpdated: Fungsi callback yang dijalankan setelah nilai input berubah
                                if ($get('purchase_date') && $state) {
                                    $years = match ($state) {
                                        'Software' => 1,
                                        'IT Equipment' => 5,
                                        'Furniture' => 10,
                                        'Vehicles' => 10,
                                        'Machinery' => 10,
                                        default => 5,
                                    };
                                    $set('eol_date', Carbon::parse($get('purchase_date'))->addYears($years)->toDateString());
                                }
                            }),

                        Select::make('department') // Select: Komponen dropdown untuk memilih opsi
                            ->label('Departemen Pengguna') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Pilih Departemen...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                                'IT' => 'IT',
                                'HR' => 'HR',
                                'Finance' => 'Finance',
                                'Operations' => 'Operations',
                                'Marketing' => 'Marketing',
                                'General' => 'General',
                            ])
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->prefixIcon('heroicon-m-building-office-2') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->searchable(), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                        TextInput::make('brand') // TextInput: Komponen input teks biasa
                            ->label('Merek / Tipe') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Contoh: Lenovo Thinkpad T14') // placeholder: Teks abu-abu panduan saat input kosong
                            ->prefixIcon('heroicon-m-swatch'), // prefixIcon: Ikon yang ditampilkan di bagian depan komponen

                        TextInput::make('serial_number') // TextInput: Komponen input teks biasa
                            ->label('Serial Number / Lisensi') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Masukkan SN atau Lisensi Key') // placeholder: Teks abu-abu panduan saat input kosong
                            ->prefixIcon('heroicon-m-hashtag') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->unique(ignoreRecord: true), // unique: Memastikan nilai unik di dalam database

                        Select::make('status') // Select: Komponen dropdown untuk memilih opsi
                            ->label('Status Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Pilih Status Saat Ini...') // placeholder: Teks abu-abu panduan saat input kosong
                            ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                                'Active' => 'Aktif',
                                'Maintenance' => 'Dalam Perbaikan',
                                'End of Life' => 'Pensiun (EOL)',
                                'Disposed' => 'Dihapus',
                                'Lost' => 'Hilang',
                            ])
                            ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                            ->prefixIcon('heroicon-m-check-badge') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->default('Active') // default: Nilai bawaan (awal) jika tidak ada input
                            ->required(), // required: Menandakan bahwa field ini wajib diisi

                        Select::make('created_by')
                            ->label('Dibuat Oleh')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-m-user'),
                    ])
                    ->columns(2), // columns: Menentukan jumlah grid/kolom

                Group::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    Section::make('Siklus Hidup (End of Life)') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                        ->schema([
                            DatePicker::make('purchase_date') // DatePicker: Komponen input tanggal
                                ->label('Tanggal Pembelian') // label: Teks label yang ditampilkan untuk komponen ini
                                ->placeholder('Pilih Tanggal Beli...') // placeholder: Teks abu-abu panduan saat input kosong
                                ->default(now()) // default: Nilai bawaan (awal) jika tidak ada input
                                ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                                ->displayFormat('d F Y') // displayFormat: Format tampilan (misal format penulisan tanggal)
                                ->prefixIcon('heroicon-m-calendar-days') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                                ->live(onBlur: true) // live: Merespon perubahan input secara real-time ke server
                                ->afterStateUpdated(function (Get $get, Set $set, $state) { // afterStateUpdated: Fungsi callback yang dijalankan setelah nilai input berubah
                                    if ($state && $get('category')) {
                                        $years = match ($get('category')) {
                                            'Software' => 1,
                                            'IT Equipment' => 5,
                                            'Furniture' => 10,
                                            'Vehicles' => 10,
                                            'Machinery' => 10,
                                            default => 5,
                                        };
                                        $set('eol_date', Carbon::parse($state)->addYears($years)->toDateString());
                                    }
                                }),

                            DatePicker::make('eol_date') // DatePicker: Komponen input tanggal
                                ->label('Tanggal End of Life (EOL)') // label: Teks label yang ditampilkan untuk komponen ini
                                ->placeholder('Pilih Tanggal EOL...') // placeholder: Teks abu-abu panduan saat input kosong
                                ->helperText('Batas waktu aman pemakaian / expired lisensi. Dapat diubah manual jika perlu.') // helperText: Teks bantuan kecil di bawah komponen
                                ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                                ->displayFormat('d F Y') // displayFormat: Format tampilan (misal format penulisan tanggal)
                                ->prefixIcon('heroicon-m-exclamation-triangle'), // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                        ])
                        ->columns(2), // columns: Menentukan jumlah grid/kolom

                    Section::make('Tambahan') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                        ->schema([
                            Textarea::make('description') // Textarea: Komponen input teks panjang
                                ->label('Catatan Tambahan') // label: Teks label yang ditampilkan untuk komponen ini
                                ->placeholder('Masukkan catatan khusus, kelengkapan, dll...') // placeholder: Teks abu-abu panduan saat input kosong
                                ->rows(3),

                            FileUpload::make('attachments') // FileUpload: Komponen untuk mengunggah file
                                ->label('Lampiran File (Foto/Dokumen)') // label: Teks label yang ditampilkan untuk komponen ini
                                ->multiple() // multiple: Mengizinkan input/pilihan lebih dari satu
                                ->directory('asset-attachments') // directory: Folder tujuan penyimpanan file unggahan
                                ->panelLayout('grid'), // panelLayout: Tata letak tampilan (contoh: grid)
                        ])
                ])
            ]);
    }
}
