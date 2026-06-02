<?php

namespace App\Filament\Resources\SoftwareLicenseDetails;

use App\Filament\Resources\SoftwareLicenseDetails\Pages\ManageSoftwareLicenseDetails;
use App\Models\SoftwareLicenseDetail;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section; // <-- SOP Filament 4
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class SoftwareLicenseDetailResource extends Resource
{
    protected static ?string $model = SoftwareLicenseDetail::class;

    // Menggunakan ikon Kunci untuk merepresentasikan Lisensi
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    // Masukkan ke grup Asset Management
    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    // Ubah dari 'name' (yang tidak ada) ke relasi nama aset agar pencarian tidak error
    protected static ?string $recordTitleAttribute = 'asset.name';

    protected static ?string $modelLabel = 'Detail Lisensi Software';
    protected static ?string $pluralModelLabel = 'Lisensi Software';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lisensi')
                    ->description('Kelola detail penggunaan kursi (seats) dan status lisensi perangkat lunak.')
                    ->icon(Heroicon::OutlinedKey)
                    ->schema([
                        Select::make('asset_id')
                            ->relationship(
                                name: 'asset',
                                titleAttribute: 'name',
                                // Optimasi UX: Hanya tampilkan Aset yang tipenya 'Software' di dropdown ini
                                modifyQueryUsing: fn(Builder $query) => $query->where('asset_type', 'Software')
                            )
                            ->label('Aset Software Terkait')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('license_type')
                            ->label('Tipe Lisensi')
                            ->options([
                                'Perpetual' => 'Perpetual (Sekali Beli)',
                                'SaaS' => 'SaaS (Langganan / Cloud)',
                                'Volume Licensing' => 'Volume Licensing (Banyak User)',
                                'OEM' => 'OEM (Bawaan Hardware)',
                                'Open Source' => 'Open Source (Gratis)',
                            ])
                            ->searchable()
                            ->placeholder('Pilih tipe lisensi...'),

                        TextInput::make('seats_count')
                            ->label('Jumlah Seat / Kuota User')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->suffix('User(s)'),

                        DatePicker::make('activated_at')
                            ->label('Tanggal Aktivasi')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->default(now()),

                        Toggle::make('is_active')
                            ->label('Lisensi Aktif/Berlaku?')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Lisensi')
                    ->schema([
                        TextEntry::make('asset.name')
                            ->label('Nama Software')
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('license_type')
                            ->label('Tipe Lisensi')
                            ->badge()
                            ->color('info')
                            ->placeholder('Tidak ditentukan'),

                        TextEntry::make('seats_count')
                            ->label('Kuota User (Seats)')
                            ->numeric()
                            ->suffix(' Orang'),

                        TextEntry::make('activated_at')
                            ->label('Diaktifkan Pada')
                            ->date('d M Y')
                            ->placeholder('Belum diaktifkan'),

                        IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean()
                            ->trueIcon(Heroicon::CheckCircle)
                            ->trueColor('success')
                            ->falseIcon(Heroicon::XCircle)
                            ->falseColor('danger'),

                        // Timestamps dihapus karena tidak ada di DBML
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Software')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    // Gabungkan tipe lisensi di bawah nama software
                    ->description(fn(SoftwareLicenseDetail $record) => 'Tipe: ' . ($record->license_type ?? 'N/A')),

                TextColumn::make('seats_count')
                    ->label('Kuota Seats')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->alignment('center'),

                TextColumn::make('activated_at')
                    ->label('Tanggal Aktivasi')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->trueColor('success')
                    ->falseIcon(Heroicon::XCircle)
                    ->falseColor('danger'),
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
            ->defaultSort('asset.name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            // Menggunakan Modal Pop-up (Manage) karena ini adalah tabel ekstensi/detail dari Asset
            'index' => ManageSoftwareLicenseDetails::route('/'),
        ];
    }
}
