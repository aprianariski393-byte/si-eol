<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Pages\ViewAsset;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Filament\Resources\Assets\Schemas\AssetInfolist;
use App\Filament\Resources\Assets\Tables\AssetsTable;
use App\Models\Asset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    // --- PENGATURAN NAVIGASI & TAMPILAN GLOBAL ---

    // Gunakan ikon 'Server' atau 'Cube' yang melambangkan aset/inventaris
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    // Sesuai SOP: Pengelompokan navigasi menggunakan UnitEnum|null
    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    // Label yang muncul di menu sebelah kiri
    protected static ?string $navigationLabel = 'Inventaris Aset';

    // Nama jamak dan tunggal untuk breadcrumb dan judul halaman
    protected static ?string $pluralModelLabel = 'Daftar Aset';
    protected static ?string $modelLabel = 'Aset';

    // Atribut utama yang digunakan untuk pencarian global (Global Search Filament)
    protected static ?string $recordTitleAttribute = 'name';

    // Batas jumlah item yang muncul di hasil Global Search
    protected static int $globalSearchResultsLimit = 10;

    /**
     * Konfigurasi form untuk membuat atau mengedit aset.
     */
    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    /**
     * Konfigurasi tampilan informasi detail dari sebuah aset.
     */
    public static function infolist(Schema $schema): Schema
    {
        return AssetInfolist::configure($schema);
    }

    /**
     * Konfigurasi tabel untuk menampilkan daftar aset.
     */
    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    /**
     * Mengambil daftar relasi (relations) yang terkait dengan resource ini.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Mendefinisikan rute dan halaman-halaman yang tersedia untuk resource aset.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
