<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class UserInfolist
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /*
                |--------------------------------------------------------------------------
                | Section: Profil Pengguna
                |--------------------------------------------------------------------------
                */
                Section::make(__('user.profile_section')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->description(__('user.profile_section_desc'))
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('avatar_url') // ImageEntry: Menampilkan nilai berupa gambar/foto
                                    ->label(__('user.avatar')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->disk('public')
                                    ->visibility('public')
                                    ->circular()
                                    ->defaultImageUrl(
                                        fn($record) =>
                                        'https://ui-avatars.com/api/?name=' .
                                        urlencode($record->name) .
                                        '&background=030712&color=FFFFFF'
                                    )
                                    ->columnSpan(1),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name') // TextEntry: Menampilkan nilai berupa teks
                                            ->label(__('user.name')) // label: Teks label yang ditampilkan untuk komponen ini
                                            ->inlineLabel()
                                            ->weight('bold'),

                                        TextEntry::make('email') // TextEntry: Menampilkan nilai berupa teks
                                            ->label(__('user.email')) // label: Teks label yang ditampilkan untuk komponen ini
                                            ->inlineLabel()
                                            ->icon('heroicon-o-envelope')
                                            ->copyable() // copyable: Memberikan fitur klik-untuk-menyalin pada data
                                            ->copyMessage(__('user.email_copied'))
                                            ->color('primary'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpan(1),

                Group::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    /*
                    |--------------------------------------------------------------------------
                    | Section: Informasi Akun
                    |--------------------------------------------------------------------------
                    */
                    Section::make(__('user.account_section')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                        ->description(__('user.account_section_desc'))
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('roles.name') // TextEntry: Menampilkan nilai berupa teks
                                        ->label(__('user.role')) // label: Teks label yang ditampilkan untuk komponen ini
                                        ->badge() // badge: Menampilkan item dengan gaya badge warna
                                        ->separator(', ')
                                        ->color('info'),

                                    TextEntry::make('email_verified_at') // TextEntry: Menampilkan nilai berupa teks
                                        ->label(__('user.email_verification_status')) // label: Teks label yang ditampilkan untuk komponen ini
                                        ->placeholder(__('user.email_verification_status_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                                        ->badge() // badge: Menampilkan item dengan gaya badge warna
                                        ->formatStateUsing( // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                                            fn($state) => $state
                                            ? __('user.email_verified')
                                            : __('user.email_not_verified')
                                        )
                                        ->color(fn($state) => $state ? 'success' : 'danger'),
                                ]),
                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | Section: Metadata Sistem
                    |--------------------------------------------------------------------------
                    */
                    Section::make(__('user.meta_section')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                        ->description(__('user.meta_section_desc'))
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('created_at') // TextEntry: Menampilkan nilai berupa teks
                                        ->label(__('user.created_at')) // label: Teks label yang ditampilkan untuk komponen ini
                                        ->icon('heroicon-o-calendar')
                                        ->formatStateUsing( // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                                            fn($state) => $state?->diffForHumans() ?? '-'
                                        ),

                                    TextEntry::make('updated_at') // TextEntry: Menampilkan nilai berupa teks
                                        ->label(__('user.updated_at')) // label: Teks label yang ditampilkan untuk komponen ini
                                        ->icon('heroicon-o-arrow-path')
                                        ->formatStateUsing( // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                                            fn($state) => $state?->diffForHumans() ?? '-'
                                        ),
                                ]),
                        ]),
                ])

            ]);
    }
}
