<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EditProfile extends BaseEditProfile
{
    /**
     * Konfigurasi form untuk resource ini.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('EditProfile')
                    ->tabs([
                        Tab::make('Informasi Pribadi')
                            ->schema([
                                FileUpload::make('avatar_url') // FileUpload: Komponen untuk mengunggah file
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.avatar.label')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->imageCropAspectRatio('1:1')
                                    ->directory('avatar_upload') // directory: Folder tujuan penyimpanan file unggahan
                                    ->visibility('public')
                                    ->helperText(__('filament-panels::pages/auth/edit-profile.form.avatar.helper')) // helperText: Teks bantuan kecil di bawah komponen
                                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid

                                TextInput::make('name') // TextInput: Komponen input teks biasa
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.name.label')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->placeholder(__('filament-panels::pages/auth/edit-profile.form.name.placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                                    ->inlineLabel()
                                    ->required() // required: Menandakan bahwa field ini wajib diisi
                                    ->maxLength(255) // maxLength: Batas maksimal jumlah karakter
                                    ->autofocus(),

                                TextInput::make('email') // TextInput: Komponen input teks biasa
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.email.label')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->placeholder(__('filament-panels::pages/auth/edit-profile.form.email.placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                                    ->inlineLabel()
                                    ->email() // email: Memvalidasi input agar berformat email yang benar
                                    ->required() // required: Menandakan bahwa field ini wajib diisi
                                    ->maxLength(255) // maxLength: Batas maksimal jumlah karakter
                                    ->unique(ignoreRecord: true), // unique: Memastikan nilai unik di dalam database
                            ]),

                        Tab::make('Kata Sandi')
                            ->schema([
                                TextInput::make('password') // TextInput: Komponen input teks biasa
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.password.label')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->placeholder(__('filament-panels::pages/auth/edit-profile.form.password.placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                                    ->dehydrated(fn ($state) => filled($state)),
                            ])
                    ]),
            ]);
    }
}
