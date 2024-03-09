<?php

namespace App\Enums;

enum CarTransmission: string
{
    case Automatic = 'Automatic';
    case Manual = 'Manual';

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
