<?php

namespace App\Enums;

enum ReportType: string
{
    case SPAM = 'Spam';
    case HARASSMENT = 'Harassment';
    case HATE_SPEECH = 'Hate Speech';
    case VIOLENCE = 'Violence';
    case COPYRIGHT_VIOLATION = 'Copyright Violation';
    case OTHER = 'Other'; // This will allow users to specify their report

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
