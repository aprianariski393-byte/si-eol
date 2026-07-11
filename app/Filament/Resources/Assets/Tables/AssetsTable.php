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
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([ // columns: Menentukan jumlah grid/kolom
                TextColumn::make('name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Identitas Aset') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable(['name', 'asset_code']) // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn($record) => 'Kode: ' . $record->asset_code)
                    ->wrap(), // wrap: Memaksa teks panjang untuk dilipat ke baris bawah

                TextColumn::make('category') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Kategori') // label: Teks label yang ditampilkan untuk komponen ini
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
                    ->formatStateUsing(fn (string $state): string => match ($state) { // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                        'IT Equipment' => 'Peralatan IT',
                        'Software' => 'Perangkat Lunak',
                        'Furniture' => 'Mebel',
                        'Vehicles' => 'Kendaraan',
                        'Machinery' => 'Mesin',
                        default => $state,
                    })
                    ->searchable(), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                TextColumn::make('status') // TextColumn: Kolom untuk menampilkan data teks biasa
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
                    ->searchable(), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                TextColumn::make('department') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Departemen Pengguna') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable(), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                TextColumn::make('eol_date') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('End of Life (EOL)') // label: Teks label yang ditampilkan untuk komponen ini
                    ->date('d M Y') // date: Format data sebagai tanggal saja
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
                    ->color(
                        fn($state) =>
                        ($state && \Carbon\Carbon::parse($state) <= now()->addMonths(3)) ? 'danger' : 'success'
                    ),

                TextColumn::make('brand') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Merek') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom

                TextColumn::make('serial_number') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Serial/Lisensi') // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    ->copyable() // copyable: Memberikan fitur klik-untuk-menyalin pada data
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom

                TextColumn::make('purchase_date') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Tgl Beli') // label: Teks label yang ditampilkan untuk komponen ini
                    ->date('d M Y') // date: Format data sebagai tanggal saja
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom

                TextColumn::make('created_at') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label('Didaftarkan Pada') // label: Teks label yang ditampilkan untuk komponen ini
                    ->dateTime('d M Y') // dateTime: Format data sebagai tanggal dan waktu
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori') // label: Teks label yang ditampilkan untuk komponen ini
                    ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                        'IT Equipment' => 'Peralatan IT',
                        'Software' => 'Perangkat Lunak',
                        'Furniture' => 'Mebel',
                        'Vehicles' => 'Kendaraan',
                        'Machinery' => 'Mesin',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Aset') // label: Teks label yang ditampilkan untuk komponen ini
                    ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                        'Active' => 'Aktif',
                        'Maintenance' => 'Dalam Perbaikan',
                        'End of Life' => 'Pensiun (EOL)',
                        'Disposed' => 'Dihapus',
                        'Lost' => 'Hilang',
                    ])
                    ->multiple(), // multiple: Mengizinkan input/pilihan lebih dari satu

                \Filament\Tables\Filters\Filter::make('eol_status')
                    ->form([
                        \Filament\Forms\Components\Select::make('status') // Select: Komponen dropdown untuk memilih opsi
                            ->options([ // options: Daftar pilihan yang tersedia untuk dropdown
                                'aman' => 'Aman (> 3 Bulan)',
                                'warning' => 'Mendekati EOL (≤ 3 Bulan)',
                                'kritis' => 'Kritis / Sudah EOL',
                            ])
                            ->label('Status EOL') // label: Teks label yang ditampilkan untuk komponen ini
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
                    ->label('Pensiunkan (EOL)') // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Pensiunkan Aset EOL')
                    ->modalDescription('Apakah Anda yakin ingin mengubah status aset ini menjadi End of Life (Pensiun)?')
                    ->action(function ($record) {
                        $record->update(['status' => 'End of Life']);
                    })
                    ->visible(fn($record) => $record->eol_date && \Carbon\Carbon::parse($record->eol_date) <= now()->addMonths(3) && !in_array($record->status, ['End of Life', 'Disposed'])), // visible: Menampilkan field berdasarkan kondisi tertentu

                Action::make('cetak_detail')
                    ->label('Cetak Detail') // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('asset.cetakDetailPdf', $record->id))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('eol_date', 'asc');
    }
}
