<?php

namespace App\Enums;

enum CarFuelType: string
{
    case CNGHYBRID = 'CNG & Hybrids';
    case Diesel = 'Diesel';
    case LPG = 'LPG';
    case Electric = 'Electric';
    case Petrol = 'Petrol';
    case Others = 'Others';

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
