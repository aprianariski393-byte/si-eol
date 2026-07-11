<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    /**
     * Mendapatkan daftar aksi (actions) pada bagian header halaman.
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('permission.delete_permission')) // label: Teks label yang ditampilkan untuk komponen ini
                ->icon(Heroicon::Trash),
        ];
    }
}
