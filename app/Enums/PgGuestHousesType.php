<?php

namespace App\Enums;

enum PgGuestHousesType: string
{
    case GUEST_HOUSES = 'Guest House';
    case PG = 'PG';
    case ROOMMATE = 'Roommate';

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
