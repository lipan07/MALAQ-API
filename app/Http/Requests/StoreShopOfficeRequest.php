<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShopOfficeRequest extends FormRequest
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
            'furnishing' => 'nullable|string|max:255',
            'listed_by' => 'nullable|string|max:255',
            'super_builtup_area' => 'nullable|integer',
            'carpet_area' => 'nullable|integer',
            'monthly_maintenance' => 'nullable|numeric',
            'car_parking' => 'nullable|integer',
            'washroom' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric',
        ];
    }
}
