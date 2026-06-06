<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Models\Domain;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DomainResource;

class ListDomains extends ListRecords
{
    protected static string $resource = DomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Domains')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Textarea::make('urls')
                        ->label('Domains (one per line)')
                        ->required()
                        ->rows(10)
                        ->placeholder("https://google.com\nhttps://github.com")
                        ->rules([
                            function () {
                                return function (string $attribute, mixed $value, \Closure $fail) {
                                    $urls = array_filter(array_map('trim', explode("\n", $value)));

                                    foreach ($urls as $url) {
                                        if (! preg_match('/^https?:\/\//', $url)) {
                                            $fail("Invalid URL: \"{$url}\". Must start with http:// or https://");

                                            return;
                                        }

                                        if (! filter_var($url, FILTER_VALIDATE_URL)) {
                                            $fail("Invalid URL format: \"{$url}\"");

                                            return;
                                        }
                                    }
                                };
                            },
                        ]),
                ])
                ->action(function (array $data) {
                    $urls = array_filter(
                        array_map('trim', explode("\n", $data['urls']))
                    );

                    foreach ($urls as $url) {
                        if (preg_match('/^https?:\/\//', $url)) {
                            Domain::create([
                                'user_id' => auth()->id(),
                                'url' => $url,
                            ]);
                        }
                    }
                }),
            CreateAction::make(),
        ];
    }
}
