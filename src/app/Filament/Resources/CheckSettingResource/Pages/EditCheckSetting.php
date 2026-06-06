<?php

namespace App\Filament\Resources\CheckSettingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CheckSettingResource;

class EditCheckSetting extends EditRecord
{
    protected static string $resource = CheckSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
