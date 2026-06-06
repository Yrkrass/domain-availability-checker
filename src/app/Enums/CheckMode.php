<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CheckMode: string implements HasColor, HasLabel
{
    case Auto = 'auto';
    case Manual = 'manual';

    public function getColor(): string
    {
        return match ($this) {
            CheckMode::Auto => 'success',
            CheckMode::Manual => 'warning',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            CheckMode::Auto => 'Auto',
            CheckMode::Manual => 'Manual',
        };
    }
}
