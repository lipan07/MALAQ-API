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
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
