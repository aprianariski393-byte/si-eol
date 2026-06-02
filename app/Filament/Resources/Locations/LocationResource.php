<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\ManageLocations;
use App\Models\Location;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- Sesuai SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    // Menggunakan ikon Map Pin yang sangat cocok untuk merepresentasikan Lokasi
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    // Masukkan ke grup Master Data
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Lokasi';
    protected static ?string $pluralModelLabel = 'Daftar Lokasi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Lokasi')
                    ->description('Kelola daftar tempat fisik aset berada.')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->placeholder('Contoh: Ruang Server Lt. 2 / Gudang IT / Plant Area')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // Nama lokasi tidak boleh duplikat
                    ])
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lokasi')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lokasi')
                            ->weight('bold')
                            ->color('primary'),
                    ])
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon(Heroicon::OutlinedMap), // Tambahkan ikon kecil di dalam sel tabel agar manis

                // Kolom timestamps (created_at & updated_at) dihapus untuk mencegah SQL Error
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
            ->defaultSort('name', 'asc'); // Urutkan secara alfabetis by default
    }

    public static function getPages(): array
    {
        return [
            // Tetap menggunakan Modal Pop-up (Manage) karena UI-nya akan terasa jauh lebih cepat
            'index' => ManageLocations::route('/'),
        ];
    }
}
