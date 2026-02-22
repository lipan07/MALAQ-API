<?php

namespace App\Models;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EngloContent extends Model
{
    use HasUuids;

    protected $table = 'englo_contents';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'genre_id',
        'language_id',
        'video_path',
        'data',
    ];

    protected $casts = [
        'genre_id' => 'integer',
        'language_id' => 'integer',
        'data' => 'array',
    ];

    protected $appends = ['video_url'];

    public function getVideoUrlAttribute(): ?string
    {
        return app(\App\Services\EngloVideoService::class)->videoUrl($this->video_path);
    }

    public function genre(): EngloGenre
    {
        return EngloGenre::from($this->genre_id);
    }

    public function language(): EngloLanguage
    {
        return EngloLanguage::from($this->language_id);
    }
}
