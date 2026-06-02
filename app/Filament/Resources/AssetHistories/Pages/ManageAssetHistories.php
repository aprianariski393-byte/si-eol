<?php

namespace App\Filament\Resources\AssetHistories\Pages;

use App\Filament\Resources\AssetHistories\AssetHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAssetHistories extends ManageRecords
{
    protected static string $resource = AssetHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
