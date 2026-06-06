<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CheckMethod: string implements HasColor, HasLabel
{
    case Head = 'head';
    case Get = 'get';
    case Both = 'both';

    public function getLabel(): string
    {
        return match ($this) {
            CheckMethod::Head => 'HEAD',
            CheckMethod::Get => 'GET',
            CheckMethod::Both => 'HEAD → GET fallback',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            CheckMethod::Head => 'info',
            CheckMethod::Get => 'warning',
            CheckMethod::Both => 'success',
        };
    }
}
