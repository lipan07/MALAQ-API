<?php

namespace App\Enums;

enum PostType: string
{
    case Rent = 'rent';
    case Sell = 'sell';
    case Donate = 'donate';
    case Requirement = 'post_requirement';

    /**
     * Returns the default type.
     *
     * @return PostType
     */
    public static function defaultType(): PostType
    {
        return self::Sell;
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
