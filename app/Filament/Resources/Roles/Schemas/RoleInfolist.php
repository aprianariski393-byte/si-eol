<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoleInfolist
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('role.name')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->weight('bold'),

                TextEntry::make('guard_name') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('role.guard')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
                    ->color('gray'),

                TextEntry::make('created_at') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('role.created_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->dateTime() // dateTime: Format data sebagai tanggal dan waktu
                    ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong

                TextEntry::make('updated_at') // TextEntry: Menampilkan nilai berupa teks
                    ->label(__('role.updated_at')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->dateTime() // dateTime: Format data sebagai tanggal dan waktu
                    ->placeholder('-'), // placeholder: Teks abu-abu panduan saat input kosong
            ]);
    }
}
