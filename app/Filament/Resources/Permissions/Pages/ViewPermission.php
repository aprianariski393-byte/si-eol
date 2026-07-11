<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    /**
     * Mendapatkan daftar aksi (actions) pada bagian header halaman.
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label(__('permission.edit_permission')) // label: Teks label yang ditampilkan untuk komponen ini
                ->icon(Heroicon::PencilSquare),
        ];
    }
}
