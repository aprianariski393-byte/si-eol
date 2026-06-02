<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    // Menggunakan ikon Tag yang sangat cocok untuk "Kategori"
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    // Masukkan ke grup Master Data
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategori Aset';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kategori')
                    ->description('Kelola pengelompokan jenis aset.')
                    ->icon(Heroicon::OutlinedTag)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Perangkat Jaringan / Lisensi OS')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true), // Kategori tidak boleh kembar

                        TextInput::make('code')
                            ->label('Kode Kategori')
                            ->placeholder('Contoh: NET / OS')
                            ->maxLength(10)
                            // Trik UX: Memaksa teks menjadi huruf kapital saat diinput user
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;']),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Kategori')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('code')
                            ->label('Kode')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('Kode disalin!')
                            ->placeholder('Tidak ada kode'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // <-- Sesuai SOP Filament 4
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('code')
                    ->label('Kode Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                // Kolom created_at & updated_at dihapus karena tidak ada di DBML untuk tabel ini
            ])
            ->filters([
                // Filter tidak terlalu dibutuhkan untuk tabel referensi sederhana
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
            // Menggunakan tipe "Manage" page yang berarti modal popup (bukan pindah halaman)
            // Sangat cocok untuk Master Data sederhana seperti ini
            'index' => ManageCategories::route('/'),
        ];
    }
}
