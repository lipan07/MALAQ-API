<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchPostsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'category' => 'nullable|integer|min:1', // Optional but must be a positive integer
            'search' => 'nullable|string|max:255', // Optional but must be a string
        ];
    }

    /**
     * Customize validation error messages.
     */
    public function messages(): array
    {
        return [
            'category.integer' => 'The category must be a valid integer.',
            'category.min' => 'The category must be greater than zero.',
            'search.string' => 'The search term must be a valid string.',
            'search.max' => 'The search term must not exceed 255 characters.',
        ];
    }
}
