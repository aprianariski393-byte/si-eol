<?php

namespace App\Filament\Resources\AssetHistories;

use App\Filament\Resources\AssetHistories\Pages\ManageAssetHistories;
use App\Models\AssetHistory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- Sesuai Standar Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AssetHistoryResource extends Resource
{
    protected static ?string $model = AssetHistory::class;

    // Gunakan ikon Jam/Waktu untuk merepresentasikan Riwayat
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    // Pengelompokan Menu Sesuai Standar
    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    // Ubah dari 'name' ke 'action' karena tidak ada kolom name
    protected static ?string $recordTitleAttribute = 'action';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Log Aktivitas')
                    ->description('Detail riwayat perubahan atau pergerakan aset.')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->schema([
                        Select::make('asset_id')
                            ->relationship('asset', 'name')
                            ->label('Aset Terkait')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Aktor (Pengguna)')
                            ->searchable()
                            ->preload()
                            ->hint('Kosongkan jika sistem yang melakukan aksi.'),

                        TextInput::make('action')
                            ->label('Aksi / Tindakan')
                            ->placeholder('Contoh: Status Changed, Location Updated')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),

                        // Gunakan KeyValue untuk memanipulasi data Array/JSON dengan UI tabel yang elegan
                        KeyValue::make('old_value')
                            ->label('Data Lama')
                            ->keyLabel('Atribut')
                            ->valueLabel('Nilai Sebelumnya'),

                        KeyValue::make('new_value')
                            ->label('Data Baru')
                            ->keyLabel('Atribut')
                            ->valueLabel('Nilai Terbaru'),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Riwayat')
                    ->schema([
                        TextEntry::make('asset.name')
                            ->label('Aset')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('user.name')
                            ->label('Diubah Oleh')
                            ->badge()
                            ->color('gray')
                            ->placeholder('Sistem / Otomatis'),

                        TextEntry::make('action')
                            ->label('Tindakan')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('created_at')
                            ->label('Waktu Kejadian')
                            ->dateTime('d M Y, H:i:s'),

                        // KeyValueEntry untuk menampilkan perbandingan data JSON
                        KeyValueEntry::make('old_value')
                            ->label('Kondisi Sebelumnya')
                            ->columnSpanFull(),

                        KeyValueEntry::make('new_value')
                            ->label('Kondisi Setelahnya')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') // Otomatis urutkan dari yang terbaru
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn(AssetHistory $record) => $record->created_at->diffForHumans()), // Tampilkan format "2 hours ago"

                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('action')
                    ->label('Aksi')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'created', 'ditambahkan' => 'success',
                        'updated', 'diperbarui', 'status changed' => 'warning',
                        'deleted', 'dihapus' => 'danger',
                        default => 'info',
                    }),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->placeholder('Sistem'),
            ])
            ->filters([
                // Nantinya bisa ditambahkan filter berdasarkan rentang waktu atau user
            ])
            ->recordActions([
                ViewAction::make(),
                // Riwayat umumnya tidak boleh diedit/dihapus secara manual demi integritas audit
                // Tapi jika Anda butuh, EditAction dan DeleteAction masih tersedia di sini
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAssetHistories::route('/'),
        ];
    }
}
