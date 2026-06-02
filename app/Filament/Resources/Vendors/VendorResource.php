<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Resources\Vendors\Pages\ManageVendors;
use App\Models\Vendor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    // Menggunakan ikon Toko / Perusahaan
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    // Masukkan ke grup Master Data
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Vendor Pemasok';
    protected static ?string $pluralModelLabel = 'Daftar Vendor';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Perusahaan')
                    ->description('Detail perusahaan pihak ketiga penyedia aset atau layanan.')
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Vendor / Perusahaan')
                            ->placeholder('Contoh: PT Computrade Technology / Microsoft')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('contact_person')
                            ->label('Nama Kontak (PIC)')
                            ->placeholder('Nama perwakilan vendor')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('email@vendor.com')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Nomor Telepon/HP')
                            ->placeholder('+62 812-...')
                            ->tel()
                            ->maxLength(50),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Alamat kantor pusat / cabang...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3) // Email, Phone, dan Contact Person akan berjajar rapi
                    ->columnSpanFull(), // <-- SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil Vendor')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Vendor')
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('contact_person')
                            ->label('Person in Charge (PIC)')
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('Tidak ada PIC khusus'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon(Heroicon::OutlinedEnvelope)
                            // Sihir UX: Membuat email bisa diklik dan membuka aplikasi email bawaan
                            ->url(fn($record) => $record->email ? "mailto:{$record->email}" : null)
                            ->color('info')
                            ->placeholder('Tidak ada email'),

                        TextEntry::make('phone')
                            ->label('Telepon')
                            ->icon(Heroicon::OutlinedPhone)
                            // Sihir UX: Membuat nomor telepon bisa diklik (berguna jika admin buka via HP/Tablet)
                            ->url(fn($record) => $record->phone ? "tel:{$record->phone}" : null)
                            ->color('success')
                            ->placeholder('Tidak ada telepon'),

                        TextEntry::make('address')
                            ->label('Alamat Kantor')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->placeholder('Alamat tidak dicatat')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->dateTime('d M Y')
                            ->color('gray'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Vendor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    // Memasukkan PIC ke bawah nama vendor agar tabel lebih ringkas
                    ->description(fn(Vendor $record) => 'PIC: ' . ($record->contact_person ?? 'N/A')),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable()
                    ->copyMessage('Email disalin!'),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->icon(Heroicon::OutlinedPhone)
                    ->copyable()
                    ->copyMessage('Nomor disalin!'),

                TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            // Tetap menggunakan Modal (Manage) karena ini adalah entitas referensi/master data
            'index' => ManageVendors::route('/'),
        ];
    }
}
