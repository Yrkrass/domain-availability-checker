<?php

namespace App\Filament\Resources;

use App\Models\Domain;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\DomainResource\Pages\EditDomain;
use App\Filament\Resources\DomainResource\Pages\ListDomains;
use App\Filament\Resources\DomainResource\Pages\CreateDomain;
use App\Filament\Resources\DomainResource\RelationManagers\CheckLogsRelationManager;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationGroup = 'Checker';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')
                    ->url()
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^https?:\/\//')
                    ->helperText('Must start with http:// or https://'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('url')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_available')
                    ->boolean()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('checked_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('is_available'),
            ])
            ->actions([
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
            'index' => ListDomains::route('/'),
            'create' => CreateDomain::route('/create'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
