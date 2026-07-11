<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    /**
     * Mengkonfigurasi pengaturan (schema/table/infolist) komponen ini.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([ // columns: Menentukan jumlah grid/kolom
                TextColumn::make('name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('user.user')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->formatStateUsing(function (User $record) { // formatStateUsing: Memodifikasi atau format ulang tampilan data sebelum dimunculkan

                        $nameParts = explode(' ', trim($record->name));
                        $initials = isset($nameParts[1])
                            ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                            : strtoupper(substr($nameParts[0], 0, 1));

                        $avatarUrl = $record->avatar_url
                            ? asset('storage/' . $record->avatar_url)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&color=FFFFFF&background=030712';

                        return '
                            <div class="flex items-center gap-3 min-w-[16rem]">
                                <!-- Avatar -->
                                <img
                                    src="' . $avatarUrl . '"
                                    alt="Avatar User"
                                    class="w-10 h-10 rounded-lg object-cover
                                        ring-1 ring-gray-300/40 dark:ring-white/20
                                        shadow-sm"
                                />

                                <!-- User Info -->
                                <div class="flex flex-col leading-tight">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        ' . e($record->name) . '
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ' . e($record->email) . '
                                    </span>
                                </div>
                            </div>
                            ';
                    })
                    ->html()
                    ->searchable(['name', 'email']), // searchable: Memungkinkan opsi untuk dicari melalui pencarian

                TextColumn::make('roles.name') // TextColumn: Kolom untuk menampilkan data teks biasa
                    ->label(__('user.role')) // label: Teks label yang ditampilkan untuk komponen ini
                    ->badge() // badge: Menampilkan item dengan gaya badge warna
                    ->separator(', ')
                    ->limitList(3)
                    ->wrap() // wrap: Memaksa teks panjang untuk dilipat ke baris bawah
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    EditAction::make()
                        ->label(__('user.edit_user')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::PencilSquare)
                        ->outlined(),
                    DeleteAction::make()
                        ->label(__('user.delete_user')) // label: Teks label yang ditampilkan untuk komponen ini
                        ->icon(Heroicon::Trash)
                        ->visible(fn(User $record): bool => $record->id !== Auth::id()), // visible: Menampilkan field berdasarkan kondisi tertentu
                ])
                    ->label('') // label: Teks label yang ditampilkan untuk komponen ini
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->size(Size::Small)
                    ->color('info')
                    ->outlined()
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([ // Group: Komponen untuk mengelompokkan elemen (layout murni)
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
