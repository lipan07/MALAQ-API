<?php

namespace App\Enums;

enum PropertyType: string
{
    case Apartments = 'Apartments';
    case BuilderFloors = 'Builder Floors';
    case HousesVillas = 'Houses and Villas';
    case FarmHouses = 'Farm Houses';

    /**
     * Returns the default type.
     *
     * @return PropertyType
     */
    public static function defaultType(): PropertyType
    {
        return self::Apartments;
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
