<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Validation\Rule;

class UpdateShopOfficeRequest extends FormRequest
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
            'furnishing' => ['required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'listedBy' => ['required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'constructionStatus' => ['required', 'string', Rule::in(PropertyConstructionStatus::allTypes())],
            'superBuiltUpArea' => 'nullable|integer',
            'carpetArea' => 'nullable|integer',
            'maintenance' => 'nullable|numeric',
            'carParking' => 'nullable|integer',
            'washroom' => 'nullable|string|max:255',
            'projectName' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric',
        ];
    }
}
