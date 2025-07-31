<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
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
            'guard_name' => ['required', 'string', Rule::in(CategoryGuardName::allTypes())],
            'post_type' => ['nullable', 'string', Rule::in(PostType::allTypes())],
            'adTitle' => 'required|string|max:100',
            'address' => 'required|string|max:250',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'new_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
