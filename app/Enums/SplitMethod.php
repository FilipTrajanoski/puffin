<?php

namespace App\Enums;

enum SplitMethod: string
{
    case EQUAL = 'equal';
    case BY_PERCENTAGE = 'percentage';
    case CUSTOM_AMOUNTS = 'custom';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
