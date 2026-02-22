<?php

namespace App\Enums;

enum EngloGenre: int
{
    case Drama = 1;
    case Comedy = 2;
    case Thriller = 3;
    case Romance = 4;
    case Action = 5;
    case Horror = 6;
    case ScienceFiction = 7;
    case Documentary = 8;

    /**
     * Get the slug/label for this genre.
     */
    public function label(): string
    {
        return match ($this) {
            self::Drama => 'drama',
            self::Comedy => 'comedy',
            self::Thriller => 'thriller',
            self::Romance => 'romance',
            self::Action => 'action',
            self::Horror => 'horror',
            self::ScienceFiction => 'sciencefiction',
            self::Documentary => 'documentary',
        };
    }

    /**
     * Get all genre IDs.
     *
     * @return array<int>
     */
    public static function ids(): array
    {
        return array_column(self::cases(), 'value');
    }
}
