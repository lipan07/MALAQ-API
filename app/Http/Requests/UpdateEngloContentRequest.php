<?php

namespace App\Http\Requests;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use App\Enums\EngloPodcastGenre;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEngloContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'video' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:102400'],
            'genre_id' => ['nullable', 'required_unless:podcast_genre_id,*', 'integer', Rule::in(EngloGenre::ids())],
            'language_id' => ['nullable', 'required_unless:podcast_genre_id,*', 'integer', Rule::in(EngloLanguage::ids())],
            'podcast_genre_id' => ['nullable', 'integer', Rule::in(EngloPodcastGenre::ids())],
            'data' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $fail('The data field must be valid JSON.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'video.mimetypes' => 'Video must be MP4, WebM or MOV.',
        ];
    }
}
