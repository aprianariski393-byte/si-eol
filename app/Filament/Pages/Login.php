<?php

namespace App\Filament\Pages;

use Caresome\FilamentAuthDesigner\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class Login extends BaseLogin
{
    /**
     * Konfigurasi form untuk resource ini.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('email') // TextInput: Komponen input teks biasa
                    ->label('Email') // label: Teks label yang ditampilkan untuk komponen ini
                    ->placeholder('Masukkan Email') // placeholder: Teks abu-abu panduan saat input kosong
                    ->email() // email: Memvalidasi input agar berformat email yang benar
                    ->required() // required: Menandakan bahwa field ini wajib diisi
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),

                TextInput::make('password') // TextInput: Komponen input teks biasa
                    ->label('Kata Sandi') // label: Teks label yang ditampilkan untuk komponen ini
                    ->placeholder('Masukkan Kata Sandi') // placeholder: Teks abu-abu panduan saat input kosong
                    ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
                    ->password() // password: Menyembunyikan karakter input (seperti password)
                    ->revealable(filament()->arePasswordsRevealable()) // revealable: Menambahkan tombol untuk melihat password
                    ->autocomplete('current-password')
                    ->required() // required: Menandakan bahwa field ini wajib diisi
                    ->extraInputAttributes(['tabindex' => 2]),
            ]);
    }
}
