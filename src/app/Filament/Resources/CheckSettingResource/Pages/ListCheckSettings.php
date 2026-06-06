<?php

namespace App\Filament\Resources\CheckSettingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CheckSettingResource;

class ListCheckSettings extends ListRecords
{
    protected static string $resource = CheckSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
