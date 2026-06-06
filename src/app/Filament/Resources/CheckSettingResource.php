<?php

namespace App\Filament\Resources;

use App\Enums\CheckMode;
use Filament\Forms\Form;
use App\Enums\CheckMethod;
use Filament\Tables\Table;
use App\Models\CheckSetting;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CheckSettingResource\Pages\EditCheckSetting;
use App\Filament\Resources\CheckSettingResource\Pages\ViewCheckSetting;
use App\Filament\Resources\CheckSettingResource\Pages\ListCheckSettings;
use App\Filament\Resources\CheckSettingResource\Pages\CreateCheckSetting;
use App\Filament\Resources\CheckSettingResource\RelationManagers\CheckLogsRelationManager;

class CheckSettingResource extends Resource
{
    protected static ?string $model = CheckSetting::class;

    protected static ?string $navigationGroup = 'Checker';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('interval')
                    ->required(fn ($get) => $get('mode') === CheckMode::Auto->value)
                    ->disabled(fn ($get) => $get('mode') !== CheckMode::Auto->value)
                    ->numeric()
                    ->default(60)
                    ->suffix('min'),
                TextInput::make('timeout')
                    ->required()
                    ->numeric()
                    ->default(10)
                    ->suffix('seconds'),
                Select::make('mode')
                    ->options(CheckMode::class)
                    ->required()
                    ->default(CheckMode::Manual)
                    ->live(),
                Select::make('method')
                    ->options(CheckMethod::class)
                    ->required()
                    ->default(CheckMethod::Both),
                DateTimePicker::make('starts_at')
                    ->nullable()
                    ->visible(fn ($get) => $get('mode') === CheckMode::Auto->value),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('interval')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min'),
                TextColumn::make('timeout')
                    ->numeric()
                    ->sortable()
                    ->suffix(' sec'),
                TextColumn::make('mode')
                    ->sortable()
                    ->badge(),
                TextColumn::make('method')
                    ->badge(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                IconColumn::make('is_running')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('info')
                    ->falseColor('gray'),
                IconColumn::make('is_active')
                    ->sortable()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('mode')
                    ->options(CheckMode::class),
                TernaryFilter::make('is_running'),
                TernaryFilter::make('is_active'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CheckLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCheckSettings::route('/'),
            'create' => CreateCheckSetting::route('/create'),
            'edit' => EditCheckSetting::route('/{record}/edit'),
            'view' => ViewCheckSetting::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
