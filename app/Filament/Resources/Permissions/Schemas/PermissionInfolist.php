<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('permission.permission_name')), // label: Teks label yang ditampilkan untuk komponen ini
                TextEntry::make('guard_name') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('permission.guard_name')), // label: Teks label yang ditampilkan untuk komponen ini
                TextEntry::make('created_at') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('permission.created_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->dateTime() // dateTime: Format data sebagai tanggal dan waktu
                    ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
                TextEntry::make('updated_at') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('permission.updated_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->dateTime() // dateTime: Format data sebagai tanggal dan waktu
                    ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
            ]);
    }
}
