<?php

namespace App\Enums;

enum EngloPodcastGenre: int
{
    case Business = 1;
    case Comedy = 2;
    case News = 3;
    case Storytelling = 4;
    case Interviews = 5;

    /**
     * Get the slug/label for this podcast genre (matches frontend ids).
     */
    public function label(): string
    {
        return match ($this) {
            self::Business => 'business',
            self::Comedy => 'comedy',
            self::News => 'news',
            self::Storytelling => 'storytelling',
            self::Interviews => 'interviews',
        };
    }

    /**
     * Get human-readable name.
     */
    public function name(): string
    {
        return match ($this) {
            self::Business => 'Business',
            self::Comedy => 'Comedy',
            self::News => 'News & Culture',
            self::Storytelling => 'Storytelling',
            self::Interviews => 'Interviews',
        };
    }

    /**
     * Get all podcast genre IDs.
     *
     * @return array<int>
     */
    public static function ids(): array
    {
        return array_column(self::cases(), 'value');
    }
}
