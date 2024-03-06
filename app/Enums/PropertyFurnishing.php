<?php

namespace App\Enums;

enum PropertyFurnishing: string
{
    case Furnished = 'Furnished';
    case SemiFurnished = 'Semi-Furnished';
    case Unfurnished = 'Unfurnished';

    /**
     * Returns the default type.
     *
     * @return PropertyFurnishing
     */
    public static function defaultType(): PropertyFurnishing
    {
        return self::Unfurnished;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return [self::Furnished, self::SemiFurnished, self::Unfurnished];
    }
}
