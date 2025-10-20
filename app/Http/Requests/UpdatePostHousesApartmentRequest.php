<?php

namespace App\Http\Requests;

use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFacing;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostHousesApartmentRequest extends FormRequest
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
            'propertyType' => ['required', 'string', Rule::in(PropertyType::allTypes())],
            'bedroom' => 'required|string',
            'bathroom' => 'required|string',
            'furnishing' => ['required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'constructionStatus' => ['required', 'string', Rule::in(PropertyConstructionStatus::allTypes())],
            'listedBy' => ['required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'superBuiltupArea' => 'nullable|integer',
            'carpetArea' => 'nullable|integer',
            'maintenance' => 'nullable|numeric',
            'totalFloors' => 'nullable|integer',
            'floorNo' => 'nullable|integer',
            'carParking' => 'nullable|string',
            'facing' => ['required', 'string', Rule::in(PropertyFacing::allTypes())],
            'projectName' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
