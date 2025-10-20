<?php

namespace App\Http\Requests;

use App\Enums\MobileBrand;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostMobileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:posts,id'],
            'brand' => ['required', 'string', Rule::in(MobileBrand::allTypes())],
            'year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
        ];
    }
}
