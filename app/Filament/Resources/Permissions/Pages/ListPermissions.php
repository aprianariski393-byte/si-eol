<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    /**
     * Mendapatkan daftar aksi (actions) pada bagian header halaman.
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('permission.create_permission')) // label: Teks label yang ditampilkan untuk komponen ini
                ->icon(Heroicon::PlusCircle),
        ];
    }
}
