<?php

namespace App\Enums;

enum PostStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Active = 'active';
    case Inactive = 'inactive';
    case Failed = 'failed';

    /**
     * Returns the default type.
     *
     * @return PostStatus
     */
    public static function defaultType(): PostStatus
    {
        return self::Pending;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allStatus(): array
    {
        return [self::Active, self::Inactive];
    }
}
