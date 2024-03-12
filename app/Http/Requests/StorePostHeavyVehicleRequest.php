<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostHeavyVehicleRequest extends FormRequest
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
            'post_id' => 'required|uuid|exists:posts,id',
            'title' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4',
            'condition' => 'required|string|max:255',
            'km_driven' => 'required|integer',
            'fuel_type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
        ];
    }
}
