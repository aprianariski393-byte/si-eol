<?php

namespace App\Filament\Resources\MaintenanceLogs\Pages;

use App\Filament\Resources\MaintenanceLogs\MaintenanceLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMaintenanceLogs extends ManageRecords
{
    protected static string $resource = MaintenanceLogResource::class;

    /**
     * Mendapatkan daftar aksi (actions) pada bagian header halaman.
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
