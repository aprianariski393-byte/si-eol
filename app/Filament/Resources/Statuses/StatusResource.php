<?php

namespace App\Filament\Resources\Statuses;

use App\Filament\Resources\Statuses\Pages\ManageStatuses;
use App\Models\Status;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;

    // Ikon Check Badge sangat cocok untuk "Status"
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    // Masukkan ke grup Master Data
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Status Aset';
    protected static ?string $pluralModelLabel = 'Daftar Status';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Status')
                    ->description('Tentukan nama status dan warna indikatornya saat tampil di sistem.')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Status')
                            ->placeholder('Contoh: Beroperasi, Rusak, Dalam Perbaikan, Disposed')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        // Mengubah TextInput menjadi Select agar warna sesuai standar Filament
                        Select::make('color')
                            ->label('Warna Indikator UI')
                            ->options([
                                'success' => 'Hijau (Sukses / Aman / Beroperasi)',
                                'danger'  => 'Merah (Bahaya / Rusak / Kritis)',
                                'warning' => 'Kuning (Peringatan / Maintenance)',
                                'info'    => 'Biru (Informasi / Standby)',
                                'gray'    => 'Abu-abu (Netral / Tidak Aktif)',
                                'primary' => 'Warna Tema Utama',
                            ])
                            ->default('gray')
                            ->required()
                            ->helperText('Warna ini akan digunakan sebagai warna Badge di seluruh tabel aplikasi.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // <-- SOP Filament 4
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Status')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Pratinjau Status')
                            ->badge()
                            // Sihir UX: Warna badge mengikuti nilai kolom 'color' yang dipilih
                            ->color(fn(Status $record) => $record->color ?? 'gray'),

                        // Timestamps dihapus karena tidak ada di DBML
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Status')
                    ->searchable()
                    ->sortable()
                    ->badge() // Langsung jadikan badge di tabel
                    // Sihir UX: Tampilkan badge sesuai warnanya di database
                    ->color(fn(Status $record) => $record->color ?? 'gray'),

                TextColumn::make('color')
                    ->label('Kode Warna (Sistem)')
                    ->color('gray')
                    ->searchable(),

                // Kolom timestamps dihapus mencegah SQL Error
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
            // Tetap menggunakan Modal (Manage)
            'index' => ManageStatuses::route('/'),
        ];
    }
}
