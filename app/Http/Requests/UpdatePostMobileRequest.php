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
            'post_type' => ['sometimes', 'required', 'string', Rule::in(PostType::allTypes())],
            'brand' => ['sometimes', 'required', 'string', Rule::in(MobileBrand::allTypes())],
            'year' => 'sometimes|nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'title' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'amount' => 'sometimes|nullable|numeric',
        ];
    }
}
