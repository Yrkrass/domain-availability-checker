<?php

namespace App\Filament\Resources\CheckSettingResource\Pages;

use App\Enums\CheckMode;
use Filament\Actions\Action;
use App\Jobs\CheckDomainsJob;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CheckSettingResource;

class ViewCheckSetting extends ViewRecord
{
    protected static string $resource = CheckSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('stop')
                ->label('Stop')
                ->icon('heroicon-o-stop')
                ->color('danger')
                ->hidden(fn () => ! $this->record->is_running)
                ->action(fn () => $this->record->update(['is_running' => false])),

            Action::make('run')
                ->label('Run Check')
                ->icon('heroicon-o-play')
                ->color('success')
                ->hidden(fn () => $this->record->is_running || $this->record->mode !== CheckMode::Manual)
                ->action(function () {
                    $this->record->update(['is_running' => true]);
                    CheckDomainsJob::dispatch($this->record);
                }),

            EditAction::make(),
        ];
    }
}
