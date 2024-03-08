<?php

namespace App\Enums;

enum Condition: string
{
    case New = 'New';
    case LikeNew = 'Like new';
    case Fair = 'Fair';
    case NeedsRepair = 'Needs repair';

    /**
     * Returns the default type.
     *
     * @return Condition
     */
    public static function defaultType(): Condition
    {
        return self::Fair;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return [self::New, self::LikeNew, self::Fair, self::NeedsRepair];
    }
}
