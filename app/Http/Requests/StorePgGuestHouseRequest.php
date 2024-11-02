<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PgGuestHousesType;
use App\Enums\PostType;
use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePgGuestHouseRequest extends FormRequest
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
            'pgType' => ['required', 'string', Rule::in(PgGuestHousesType::allTypes())],
            'furnishing' => ['required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'listedBy' => ['required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'construction_status' => ['required', 'string', Rule::in(PropertyConstructionStatus::allTypes())],
            'carpetArea' => 'nullable|integer',
            'isMealIncluded' => 'nullable|boolean',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric',
        ];
    }
}
