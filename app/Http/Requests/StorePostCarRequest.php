<?php

namespace App\Http\Requests;

use App\Enums\CarBrand;
use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostCarRequest extends FormRequest
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
            'post_type' => ['required', 'string', Rule::in(PostType::allTypes())],
            'address' => 'required|string|max:250',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'brand' => ['required', 'string', Rule::in(CarBrand::allTypes())],
            'year' => 'required|digits:4',
            'fuel' => 'required|string',
            'transmission' => 'required|string',
            'km_driven' => 'required|integer',
            'no_of_owner' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }
}
