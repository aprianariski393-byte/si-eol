<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AssetInfolist
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Aset') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextEntry::make('name') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Nama Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                        TextEntry::make('asset_code') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Kode Aset') // label: Teks label yang ditampilkan untuk komponen ini
                            ->copyable() // copyable: Memberikan fitur klik-untuk-menyalin pada data
                            ->copyMessage('Kode Aset disalin!')
                            ->icon(Heroicon::OutlinedDocumentDuplicate),

                        TextEntry::make('category') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Kategori') // label: Teks label yang ditampilkan untuk komponen ini
                            ->formatStateUsing(fn (string $state): string => match ($state) { // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                                'IT Equipment' => 'Peralatan IT',
                                'Software' => 'Perangkat Lunak',
                                'Furniture' => 'Mebel',
                                'Vehicles' => 'Kendaraan',
                                'Machinery' => 'Mesin',
                                default => $state,
                            })
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('brand') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Merek') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('serial_number') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Serial Number / Lisensi') // label: Teks label yang ditampilkan untuk komponen ini
                            ->copyable() // copyable: Memberikan fitur klik-untuk-menyalin pada data
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
                    ])
                    ->columns(3) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                Section::make('Siklus Hidup (End of Life)') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->icon(Heroicon::OutlinedClock)
                    ->schema([
                        TextEntry::make('purchase_date') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Tanggal Pembelian') // label: Teks label yang ditampilkan untuk komponen ini
                            ->date('d M Y') // date: Format data sebagai tanggal saja
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('eol_date') // TextEntry: Menampilkan nilai berupa teks
                            ->label('End of Life (EOL)') // label: Teks label yang ditampilkan untuk komponen ini
                            ->date('d M Y') // date: Format data sebagai tanggal saja
                            ->badge() // badge: Menampilkan item dengan gaya badge warna
                            ->color(
                                fn($record) => ($record->eol_date && \Carbon\Carbon::parse($record->eol_date) <= now()->addMonths(3)) ? 'danger' : 'success'
                            )
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
                    ])
                    ->columns(2) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                Section::make('Operasional') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        TextEntry::make('status') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Status') // label: Teks label yang ditampilkan untuk komponen ini
                            ->badge() // badge: Menampilkan item dengan gaya badge warna
                            ->formatStateUsing(fn (string $state): string => match ($state) { // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                                'Active' => 'Aktif',
                                'Maintenance' => 'Dalam Perbaikan',
                                'End of Life' => 'Pensiun (EOL)',
                                'Disposed' => 'Dihapus',
                                'Lost' => 'Hilang',
                                default => $state,
                            })
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                        TextEntry::make('department') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Departemen') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
                    ])
                    ->columns(2) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                Section::make('Informasi Tambahan') // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->schema([
                        TextEntry::make('description') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Deskripsi/Catatan') // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder('Tidak ada catatan.') // placeholder: Teks abu-abu panduan saat input kosong
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                        TextEntry::make('created_at') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Didaftarkan Pada') // label: Teks label yang ditampilkan untuk komponen ini
                            ->dateTime('d M Y, H:i') // dateTime: Format data sebagai tanggal dan waktu
                            ->color('gray'),

                        TextEntry::make('updated_at') // TextEntry: Menampilkan nilai berupa teks
                            ->label('Terakhir Diperbarui') // label: Teks label yang ditampilkan untuk komponen ini
                            ->dateTime('d M Y, H:i') // dateTime: Format data sebagai tanggal dan waktu
                            ->color('gray'),

                        TextEntry::make('creator.name')
                            ->label('Didaftarkan Oleh')
                            ->placeholder('Tidak diketahui'),
                    ])
                    ->columns(3) // columns: Menentukan jumlah grid/kolom
                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
            ]);
    }
}
