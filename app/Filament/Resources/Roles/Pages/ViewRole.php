<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Mendapatkan daftar aksi (actions) pada bagian header halaman.
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label(__('role.edit_role')) // label: Teks label yang ditampilkan untuk komponen ini
                ->icon(Heroicon::PencilSquare),
        ];
    }
}
