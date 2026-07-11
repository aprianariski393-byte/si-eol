<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RolesTable
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([ // columns: Menentukan jumlah grid/kolom
                TextColumn::make('name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('role.name')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->searchable(), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                TextColumn::make('permissions.name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('role.permissions')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->placeholder(__('role.no_permissions')) // placeholder: Teks abu-abu panduan saat input kosong
                    ->colors([ // colors: Menentukan warna berdasarkan kondisi nilai tertentu
                        'info',
                    ])
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
                    ->separator(', ')
                    ->limitList(4)
                    ->wrap(), // wrap: Memaksa teks panjang untuk dilipat ke baris bawah
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    EditAction::make()
                        ->label(__('role.edit_role')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::PencilSquare),
                    DeleteAction::make()
                        ->label(__('role.delete_role')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::Trash)
                        ->hidden(function ($record) { // hidden: Menyembunyikan field berdasarkan kondisi tertentu
                            // Ambil semua role ids yang dimiliki user saat ini
                            $userRoleIds = Auth::user()->roles->pluck('id')->toArray();
                            // Sembunyikan tombol jika role ini termasuk milik user
                            return in_array($record->id, $userRoleIds);
                        }),
                ])
                    ->label('') // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->size(Size::Small)
                    ->color('info')
                    ->outlined()
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
