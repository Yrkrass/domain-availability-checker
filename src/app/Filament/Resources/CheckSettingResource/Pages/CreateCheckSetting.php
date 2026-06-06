<?php

namespace App\Filament\Resources\CheckSettingResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CheckSettingResource;

class CreateCheckSetting extends CreateRecord
{
    protected static string $resource = CheckSettingResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }
}
