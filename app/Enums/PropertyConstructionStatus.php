<?php

namespace App\Enums;

enum PropertyConstructionStatus: string
{
    case NewLaunch = 'New Launch';
    case ReadyToMove = 'Ready to Move';
    case UnderConstruction = 'Under Construction';

    /**
     * Returns the default type.
     *
     * @return PropertyConstructionStatus
     */
    public static function defaultType(): PropertyConstructionStatus
    {
        return self::NewLaunch;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return [self::NewLaunch, self::ReadyToMove, self::UnderConstruction];
    }
}
