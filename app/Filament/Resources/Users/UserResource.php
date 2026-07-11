<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|UnitEnum|null $navigationGroup = 'Kelola Pengguna';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Users;
    /**
     * Fungsi getNavigationLabel.
     */
    public static function getNavigationLabel(): string
    {
        return __('user.user_label');
    }
    /**
     * Fungsi getModelLabel.
     */
    public static function getModelLabel(): string
    {
        return __('user.user_label');
    }
    /**
     * Fungsi getPluralModelLabel.
     */
    public static function getPluralModelLabel(): string
    {
        return __('user.users');
    }
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 21;
    protected static ?string $slug = 'users';

    /**
     * Konfigurasi form untuk resource ini.
     */
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    /**
     * Konfigurasi tampilan informasi detail data.
     */
    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    /**
     * Konfigurasi tabel untuk menampilkan daftar data.
     */
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
