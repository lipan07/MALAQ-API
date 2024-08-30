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
            'property_type' => ['sometimes', 'required', 'string', Rule::in(PropertyType::allTypes())],
            'bedrooms' => 'sometimes|nulable|integer',
            'furnishing' => ['sometimes', 'required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'construction_status' => ['sometimes', 'required', 'string', Rule::in(PropertyConstructionStatus::allTypes())],
            'listed_by' => ['sometimes', 'required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'super_builtup_area' => 'sometimes|nullable|integer',
            'carpet_area' => 'sometimes|nullable|integer',
            'monthly_maintenance' => 'sometimes|nullable|numeric',
            'total_floors' => 'sometimes|nullable|integer',
            'floor_no' => 'sometimes|nullable|integer',
            'car_parking' => 'sometimes|nullable|integer',
            'facing' => ['sometimes', 'required', 'string', Rule::in(PropertyFacing::allTypes())],
            'project_name' => 'sometimes|nullable|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'amount' => 'sometimes|required|numeric',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
