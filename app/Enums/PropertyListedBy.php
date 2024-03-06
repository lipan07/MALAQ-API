<?php

namespace App\Enums;

enum PropertyListedBy: string
{
    case Builder = 'Builder';
    case Dealer = 'Dealer';
    case Owner = 'Owner';

    /**
     * Returns the default type.
     *
     * @return PropertyListedBy
     */
    public static function defaultType(): PropertyListedBy
    {
        return self::Owner;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return [self::Builder, self::Dealer, self::Owner];
    }
}
