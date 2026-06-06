<?php

namespace App\Filament\Resources\CheckSettingResource\RelationManagers;

use App\Models\CheckLog;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ResponseCode;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Resources\RelationManagers\RelationManager;

class CheckLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'checkLogs';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->recordTitleAttribute('domain_id')
            ->columns([
                TextColumn::make('domain.url')
                    ->label('Domain')
                    ->searchable(),
                IconColumn::make('is_available')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('response_code')
                    ->badge()
                    ->formatStateUsing(fn (?int $state) => $state ? ResponseCode::labelFromCode($state) : '—')
                    ->color(fn (?int $state) => $state ? (ResponseCode::tryFrom($state)?->getColor() ?? 'gray') : 'gray'),
                TextColumn::make('response_time')
                    ->formatStateUsing(fn (?int $state) => $state !== null
                        ? ($state >= 1000 ? number_format($state / 1000, 2).' sec' : $state.' ms')
                        : '—'
                    )
                    ->sortable(),
                TextColumn::make('error')
                    ->placeholder('—')
                    ->limit(30)
                    ->searchable()
                    ->extraAttributes(fn (CheckLog $record) => $record->error ? [
                        'class' => 'underline cursor-pointer',
                    ] : [])
                    ->action(
                        Action::make('viewError')
                            ->label('Error')
                            ->modalContent(fn (CheckLog $record) => new HtmlString('<p>'.e($record->error).'</p>'))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_available'),
                SelectFilter::make('response_code')
                    ->options(ResponseCode::class),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
