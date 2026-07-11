<?php

namespace App\Filament\Resources\Permissions\Tables;

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

class PermissionsTable
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([ // columns: Menentukan jumlah grid/kolom
                TextColumn::make('name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('permission.permission_name')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon(Heroicon::Key)
                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->weight('bold'),

                TextColumn::make('created_at') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('permission.created_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-o-calendar')
                    ->since() // “2 hours ago”
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom

                TextColumn::make('updated_at') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('permission.updated_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-o-clock')
                    ->since()
                    ->sortable() // sortable: Memungkinkan kolom diurutkan (sorting) dengan klik header tabel
                    ->toggleable(isToggledHiddenByDefault: true), // toggleable: Kolom bisa disembunyikan/dimunculkan dari pengaturan kolom
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    EditAction::make()
                        ->label(__('permission.edit_permission')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::PencilSquare),
                    DeleteAction::make()
                        ->label(__('permission.delete_permission')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::Trash),
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
