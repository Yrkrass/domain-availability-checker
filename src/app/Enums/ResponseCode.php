<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ResponseCode: int implements HasColor, HasLabel
{
    case Ok = 200;
    case MovedPermanently = 301;
    case Found = 302;
    case NotFound = 404;
    case InternalServerError = 500;

    public function getLabel(): string
    {
        return match ($this) {
            ResponseCode::Ok => '200 OK',
            ResponseCode::MovedPermanently => '301 Moved Permanently',
            ResponseCode::Found => '302 Found',
            ResponseCode::NotFound => '404 Not Found',
            ResponseCode::InternalServerError => '500 Internal Server Error',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            ResponseCode::Ok => 'success',
            ResponseCode::MovedPermanently => 'info',
            ResponseCode::Found => 'info',
            ResponseCode::NotFound => 'warning',
            ResponseCode::InternalServerError => 'danger',
        };
    }

    public static function labelFromCode(int $code): string
    {
        $enum = self::tryFrom($code);

        return $enum ? $enum->getLabel() : (string) $code;
    }
}
