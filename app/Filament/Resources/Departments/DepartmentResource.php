<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\ManageDepartments;
use App\Models\Department;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    // Menggunakan ikon Gedung Kantor untuk merepresentasikan Departemen/Divisi
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    // Masukkan ke grup Master Data (Sesuai SOP)
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Departemen';
    protected static ?string $pluralModelLabel = 'Daftar Departemen';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Departemen')
                    ->description('Kelola divisi atau bagian yang menjadi pengguna aset.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Departemen')
                            ->placeholder('Contoh: IT Support / Maintenance / HRD')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true), // Nama departemen tidak boleh duplikat
                    ])
                    // Karena hanya 1 kolom, tidak perlu ->columns(2) agar field-nya panjang dan nyaman diisi
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Departemen')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Departemen')
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
                    ->label('Nama Departemen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Kolom timestamps dihapus karena tidak dideklarasikan di DBML
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
            ->defaultSort('name', 'asc'); // Otomatis berurutan sesuai abjad
    }

    public static function getPages(): array
    {
        return [
            // Tetap menggunakan Modal Pop-up (Manage) karena ini Master Data sederhana
            'index' => ManageDepartments::route('/'),
        ];
    }
}
