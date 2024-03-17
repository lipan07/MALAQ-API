<?php

namespace App\Enums;

enum PropertyFacing: string
{
    case East = 'East';
    case North = 'North';
    case South = 'South';
    case West = 'West';
    case NorthEast = 'North-East';
    case NorthWest = 'North-West';
    case SouthEast = 'South-East';
    case SouthWest = 'South-West';

    /**
     * Returns the default type.
     *
     * @return PropertyFacing
     */
    public static function defaultType(): PropertyFacing
    {
        return self::East;
    }

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
