<?php

namespace App\Enums;

enum BycycleBrand: string
{
    case Hercules = 'Hercules';
    case Hero = 'Hero';
    case OtherBrands = 'Other Brands';

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
