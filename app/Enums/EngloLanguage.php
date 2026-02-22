<?php

namespace App\Enums;

enum EngloLanguage: int
{
    case Korean = 1;
    case Japanese = 2;
    case Bollywood = 3;
    case Hollywood = 4;
    case Chinese = 5;

    /**
     * Get the slug/label for this language.
     */
    public function label(): string
    {
        return match ($this) {
            self::Korean => 'korean',
            self::Japanese => 'japanese',
            self::Bollywood => 'bollywood',
            self::Hollywood => 'hollywood',
            self::Chinese => 'chinese',
        };
    }

    /**
     * Get all language IDs.
     *
     * @return array<int>
     */
    public static function ids(): array
    {
        return array_column(self::cases(), 'value');
    }
}
