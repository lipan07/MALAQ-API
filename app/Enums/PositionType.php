<?php

namespace App\Enums;

enum PositionType: string
{
    case Contract = 'Contract';
    case FullTime = 'Full-time';
    case PartTime = 'Part-time';
    case Temporary = 'Temporary';

    /**
     * Returns the default type.
     *
     * @return Condition
     */
    public static function defaultType(): PositionType
    {
        return self::FullTime;
    }

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
