<?php

namespace App\Http\Requests;

use App\Enums\PgGuestHousesType;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePgGuestHouseRequest extends FormRequest
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
            'pg_type' => ['sometimes', 'required', 'string', Rule::in(PgGuestHousesType::allTypes())],
            'furnishing' => ['sometimes', 'required', 'string', Rule::in(PropertyFurnishing::allTypes())],
            'listed_by' => ['sometimes', 'required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'carpet_area' => 'sometimes|nullable|integer',
            'is_meal_included' => 'sometimes|nullable|boolean',
            'title' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'amount' => 'sometimes|nullable|numeric',
        ];
    }
}
