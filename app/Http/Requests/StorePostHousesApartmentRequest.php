<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFacing;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\PropertyFetch;

class StorePostHousesApartmentRequest extends FormRequest
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

            'type' => ['required', 'string', Rule::in(PropertyType::allTypes())],
            'bedrooms' => 'integer',
            'furnishing' => ['required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'construction_status' => ['required', 'string', Rule::in(PropertyConstructionStatus::allTypes())],
            'listed_by' => ['required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'super_builtup_area' => 'nullable|integer',
            'carpet_area' => 'nullable|integer',
            'monthly_maintenance' => 'nullable|numeric',
            'total_floors' => 'nullable|integer',
            'floor_no' => 'integer',
            'car_parking' => 'integer',
            'facing' => ['required', 'string', Rule::in(PropertyFacing::allTypes())],
            'project_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'string',
            'amount' => 'numeric',
        ];
    }
}
