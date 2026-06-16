<?php

namespace App\Filament\Resources\AssetAttachments;

use App\Filament\Resources\AssetAttachments\Pages\ManageAssetAttachments;
use App\Models\AssetAttachment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class AssetAttachmentResource extends Resource
{
    protected static ?string $model = AssetAttachment::class;

    // Ubah ikon agar lebih relevan dengan "Attachment/File"
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperClip;

    // Opsional: Masukkan ke dalam grup navigasi agar sidebar rapi
    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?string $navigationLabel = 'Lampiran Aset';

    // Nama jamak dan tunggal untuk breadcrumb dan judul halaman
    protected static ?string $pluralModelLabel = 'Lampiran Aset';
    protected static ?string $modelLabel = 'Lampiran Aset';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Lampiran')
                    ->description('Pilih aset dan unggah dokumen terkait (Sertifikat, Installer, dll).')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        Select::make('asset_id')
                            ->relationship('asset', 'name')
                            ->label('Aset Terkait')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Judul Lampiran')
                            ->placeholder('Contoh: Lisensi Windows Server 2022')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('file_type')
                            ->label('Ekstensi/Tipe')
                            ->placeholder('pdf, docx, exe')
                            ->maxLength(50),

                        // Ubah ke FileUpload agar UX lebih modern
                        FileUpload::make('file_path')
                            ->label('Unggah Berkas')
                            ->directory('asset-attachments')
                            ->preserveFilenames()
                            ->openable()
                            ->downloadable()
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2) // Membagi form jadi 2 kolom (kiri-kanan)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lampiran')
                    ->schema([
                        TextEntry::make('asset.name')
                            ->label('Nama Aset')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('title')
                            ->label('Judul Lampiran'),

                        TextEntry::make('file_type')
                            ->label('Tipe Berkas')
                            ->badge()
                            ->color(fn(string $state): string => match (strtolower($state)) {
                                'pdf' => 'danger',
                                'doc', 'docx' => 'info',
                                'xls', 'xlsx' => 'success',
                                'exe', 'msi' => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('file_path')
                            ->label('Aksi Berkas')
                            ->formatStateUsing(fn() => 'Buka Berkas ↗')
                            ->color('primary')
                            ->url(fn($record) => Storage::url($record->file_path))
                            ->openUrlInNewTab(),

                        TextEntry::make('created_at')
                            ->label('Diunggah Pada')
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    // Menampilkan deskripsi kecil di bawah judul
                    ->description(fn(AssetAttachment $record): string => 'Tipe: ' . strtoupper($record->file_type ?? 'Unknown')),

                TextColumn::make('file_type')
                    ->label('Format')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'pdf' => 'danger',
                        'doc', 'docx' => 'info',
                        'xls', 'xlsx' => 'success',
                        'exe', 'msi' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('file_path')
                    ->label('Unduhan')
                    ->formatStateUsing(fn() => 'Unduh Berkas')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('primary')
                    ->url(fn($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Anda bisa menambahkan SelectFilter untuk asset_id di sini nanti
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAssetAttachments::route('/'),
        ];
    }
}
