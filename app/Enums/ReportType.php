<?php

namespace App\Enums;

enum ReportType: string
{
    case SPAM = 'spam';
    case INAPPROPRIATE = 'inappropriate';
    case FalseInfo = 'false_info';
    case OTHER = 'other'; // This will allow users to specify their report

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
