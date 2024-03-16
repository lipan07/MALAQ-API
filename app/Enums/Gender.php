<?php

namespace App\Enums;

enum Gender: string
{
    case Men = 'men';
    case Women = 'women';
    case Kids = 'kids';

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
