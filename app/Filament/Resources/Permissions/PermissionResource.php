<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Clusters\RolePermission\RolePermissionCluster;
use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Pages\ViewPermission;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Permissions\Schemas\PermissionInfolist;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use App\Models\Permission;
use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $cluster = RolePermissionCluster::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Key;
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;
    /**
     * Fungsi getNavigationLabel.
     */
    public static function getNavigationLabel(): string
    {
        return __('permission.permission_label');
    }
    /**
     * Fungsi getModelLabel.
     */
    public static function getModelLabel(): string
    {
        return __('permission.permission_label');
    }
    /**
     * Fungsi getPluralModelLabel.
     */
    public static function getPluralModelLabel(): string
    {
        return __('permission.permissions');
    }
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 19;
    protected static ?string $slug = 'permissions';


    /**
     * Konfigurasi form untuk resource ini.
     */
    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    /**
     * Konfigurasi tampilan informasi detail data.
     */
    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    /**
     * Konfigurasi tabel untuk menampilkan daftar data.
     */
    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
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
     * Mendefinisikan rute dan halaman-halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
