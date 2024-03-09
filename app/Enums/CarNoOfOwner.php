<?php

namespace App\Enums;

enum CarNoOfOwner: string
{
    case First = '1st';
    case Second = '2nd';
    case Thrid = '3rd';
    case Fourth = '4th';
    case FourthPlus = '4+';

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
