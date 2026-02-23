<?php

namespace App\Models;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use App\Enums\EngloPodcastGenre;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EngloContent extends Model
{
    use HasUuids;

    protected $table = 'englo_contents';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'genre_id',
        'language_id',
        'podcast_genre_id',
        'video_path',
        'data',
    ];

    protected $casts = [
        'genre_id' => 'integer',
        'language_id' => 'integer',
        'podcast_genre_id' => 'integer',
        'data' => 'array',
    ];

    protected $appends = ['video_url'];

    public function getVideoUrlAttribute(): ?string
    {
        if (!$this->video_path) {
            return null;
        }
        return Storage::disk('public')->url($this->video_path);
    }

    public function genre(): ?EngloGenre
    {
        return $this->genre_id === null ? null : EngloGenre::tryFrom($this->genre_id);
    }

    public function language(): ?EngloLanguage
    {
        return $this->language_id === null ? null : EngloLanguage::tryFrom($this->language_id);
    }

    public function podcastGenre(): ?EngloPodcastGenre
    {
        return $this->podcast_genre_id === null ? null : EngloPodcastGenre::tryFrom($this->podcast_genre_id);
    }
}
