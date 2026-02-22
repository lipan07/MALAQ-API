<?php

namespace App\Http\Requests;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEngloContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'], // 100MB max
            'genre_id' => ['required', 'integer', Rule::in(EngloGenre::ids())],
            'language_id' => ['required', 'integer', Rule::in(EngloLanguage::ids())],
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
            'video.mimetypes' => 'Video must be MP4, WebM or MOV. Max duration 3 minutes.',
        ];
    }
}
