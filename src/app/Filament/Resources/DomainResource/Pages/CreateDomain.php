<?php

namespace App\Filament\Resources\DomainResource\Pages;

use Filament\Facades\Filament;
use App\Filament\Resources\DomainResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }
}
