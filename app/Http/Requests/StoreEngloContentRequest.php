<?php

namespace App\Http\Requests;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use App\Enums\EngloPodcastGenre;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StoreEngloContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $ok = $user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        if (!$ok) {
            Log::warning('EngloContent StoreEngloContentRequest: unauthorized', [
                'user_id' => $user?->id,
            ]);
        }
        return $ok;
    }

    protected function prepareForValidation(): void
    {
        $hasFile = $this->hasFile('video');
        Log::info('EngloContent StoreEngloContentRequest: prepareForValidation', [
            'has_file' => $hasFile,
            'all_keys' => array_keys($this->all()),
            'file_keys' => array_keys($this->allFiles()),
            'video_valid' => $hasFile && $this->file('video')?->isValid(),
            'video_error' => $hasFile ? $this->file('video')?->getError() : null,
            'video_size' => $hasFile ? $this->file('video')?->getSize() : null,
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ]);
    }

    public function rules(): array
    {
        return [
            'video' => ['required', 'file', 'mimes:mp4,webm,mov', 'max:102400'],
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
            'video.required' => 'Please upload a video file.',
            'video.mimes' => 'Video must be MP4, WebM or MOV.',
            'video.max' => 'Video must be under 100MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('EngloContent StoreEngloContentRequest: validation failed', [
            'errors' => $validator->errors()->toArray(),
        ]);
        parent::failedValidation($validator);
    }
}
