<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make() // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    ->schema([
                        Section::make() // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                            ->schema([
                                FileUpload::make('avatar_url') // FileUpload: Komponen untuk mengunggah file
                                    ->label(__('user.avatar')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->helperText(__('user.avatar_helper')) // helperText: Teks bantuan kecil di bawah komponen
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->imageCropAspectRatio('1:1')
                                    ->disk('public')
                                    ->directory('avatar_upload') // directory: Folder tujuan penyimpanan file unggahan
                                    ->visibility('public')
                                    ->columnSpanFull(), // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ]),
                        Section::make() // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                            ->schema([
                                Select::make('roles') // Select: Komponen dropdown untuk memilih opsi
                                    ->label(__('user.role')) // label: Teks label yang ditampilkan untuk komponen ini
                                    ->placeholder(__('user.select_role')) // placeholder: Teks abu-abu panduan saat input kosong
                                    ->relationship('roles', 'name') // relationship: Mengambil data dari relasi model
                                    ->native(false) // native: Menggunakan UI custom Filament (jika false) atau bawaan browser
                                    ->preload() // preload: Memuat data terlebih dahulu sebelum pencarian
                                    ->multiple() // multiple: Mengizinkan input/pilihan lebih dari satu
                                    ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                                    ->searchable() // searchable: Memungkinkan opsi untuk dicari melalui pencarian
                                    ->required(), // required: Menandakan bahwa field ini wajib diisi
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 3,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 1,
                        '2xl' => 1,
                    ])
                    ->columns(1), // columns: Menentukan jumlah grid/kolom

                Section::make(__('user.personal_info')) // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->description(__('user.user_information_desc'))
                    ->schema([
                        TextInput::make('name') // TextInput: Komponen input teks biasa
                            ->label(__('user.name')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder(__('user.name_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                            ->inlineLabel()
                            ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->minLength(3)
                            ->maxLength(45) // maxLength: Batas maksimal jumlah karakter
                            ->autofocus(),

                        TextInput::make('email') // TextInput: Komponen input teks biasa
                            ->label(__('user.email')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder(__('user.email_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                            ->inlineLabel()
                            ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ->email() // email: Memvalidasi input agar berformat email yang benar
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->minLength(3)
                            ->maxLength(45) // maxLength: Batas maksimal jumlah karakter
                            ->unique(ignoreRecord: true), // unique: Memastikan nilai unik di dalam database
                        TextInput::make('password') // TextInput: Komponen input teks biasa
                            ->label( // label: Teks label yang ditampilkan untuk komponen ini
                                fn($record) =>
                                $record ? __('user.password_edit') : __('user.password')
                            )
                            ->placeholder( // placeholder: Teks abu-abu panduan saat input kosong
                                fn($record) =>
                                $record ? __('user.password_optional') : __('user.password_placeholder')
                            )
                            ->inlineLabel()
                            ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ->password() // password: Menyembunyikan karakter input (seperti password)
                            ->revealable(filament()->arePasswordsRevealable()) // revealable: Menambahkan tombol untuk melihat password
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn($state): bool => filled($state)) // dehydrated: Menentukan apakah data akan dikirim/disimpan ke database
                            ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                            ->live(debounce: 500) // live: Merespon perubahan input secara real-time ke server
                            ->same('passwordConfirmation')
                            ->required(fn($record) => is_null($record)), // required: Menandakan bahwa field ini wajib diisi

                        TextInput::make('passwordConfirmation') // TextInput: Komponen input teks biasa
                            ->label(__('user.password_confirm')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->placeholder(__('user.password_confirm_placeholder')) // placeholder: Teks abu-abu panduan saat input kosong
                            ->inlineLabel()
                            ->columnSpanFull() // columnSpanFull: Komponen mengambil lebar penuh pada grid
                            ->password() // password: Menyembunyikan karakter input (seperti password)
                            ->revealable(filament()->arePasswordsRevealable()) // revealable: Menambahkan tombol untuk melihat password
                            ->required() // required: Menandakan bahwa field ini wajib diisi
                            ->visible(fn(Get $get): bool => filled($get('password'))) // visible: Menampilkan field berdasarkan kondisi tertentu
                            ->dehydrated(false), // dehydrated: Menentukan apakah data akan dikirim/disimpan ke database
                    ])->columnSpan([
                            'default' => fn(?User $record) => $record === null ? 3 : 3,
                            'sm' => fn(?User $record) => $record === null ? 2 : 3,
                            'md' => fn(?User $record) => $record === null ? 3 : 3,
                            'lg' => fn(?User $record) => $record === null ? 4 : 4,
                            'xl' => fn(?User $record) => $record === null ? 3 : 2,
                            '2xl' => fn(?User $record) => $record === null ? 3 : 2,
                        ])
                    ->columns(2), // columns: Menentukan jumlah grid/kolom

                Section::make() // Section: Komponen untuk mengelompokkan elemen ke dalam blok/kartu
                    ->schema([
                        TextEntry::make('created_at') // TextEntry: Menampilkan nilai berupa teks
                            ->label(__('user.created_at')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->formatStateUsing(fn(User $record): ?string => $record->created_at?->diffForHumans()), // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan

                        TextEntry::make('updated_at') // TextEntry: Menampilkan nilai berupa teks
                            ->label(__('user.updated_at')) // label: Teks label yang ditampilkan untuk komponen ini
                            ->formatStateUsing(fn(User $record): ?string => $record->updated_at?->diffForHumans()), // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan
                    ])
                    ->columnSpan([
                        'default' => 3,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 1,
                        '2xl' => 1,
                    ])
                    ->hidden(fn(?User $record) => $record === null) // hidden: Menyembunyikan field berdasarkan kondisi tertentu
            ])->columns(4); // columns: Menentukan jumlah grid/kolom
    }
}
