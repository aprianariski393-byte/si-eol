<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PermissionForm
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('permission.permission_information')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->description(__('permission.permission_information_desc'))
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        TextInput::make('name') // TextInput: Komponen input teks biasa
                            ->label(__('permission.permission_name')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder(__('permission.permission_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                            ->helperText( // helperText: Teks bantuan kecil di bawah komponen
                                new HtmlString(__('permission.helper_text_permission'))
                            )
                            ->prefixIcon('heroicon-o-lock-closed') // prefixIcon: Ikon yang ditampilkan di bagian depan komponen
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->minLength(3)
                            ->maxLength(45) // maxLength: Batas maksimal jumlah karakter
                            ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
                    ])
                    ->columns(1) // columns: Menentukan jumlah grid/kolom
                    ->collapsible()
                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
            ]);
    }
}
