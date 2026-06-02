<?php

namespace App\Filament\Resources\SoftwareLicenseDetails\Pages;

use App\Filament\Resources\SoftwareLicenseDetails\SoftwareLicenseDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSoftwareLicenseDetails extends ManageRecords
{
    protected static string $resource = SoftwareLicenseDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
