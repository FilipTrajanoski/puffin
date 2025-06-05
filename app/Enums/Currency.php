<?php

namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case CHF = 'CHF';
    case MKD = 'MKD';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
