<?php

namespace App\Http\Requests;

use App\Enums\CarBrand;
use App\Enums\CarFuelType;
use App\Enums\CarNoOfOwner;
use App\Enums\CarTransmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostCarRequest extends FormRequest
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
            'address' => 'sometimes|required|string|max:250',
            'latitude' => 'sometimes|nullable|numeric',
            'longitude' => 'sometimes|nullable|numeric',
            'brand' => ['sometimes', 'required', 'string', Rule::in(CarBrand::allTypes())],
            'year' => 'sometimes|required|digits:4',
            'fuel' => ['sometimes', 'required', 'string', Rule::in(CarFuelType::allTypes())],
            'transmission' => ['sometimes', 'required', 'string', Rule::in(CarTransmission::allTypes())],
            'km_driven' => 'sometimes|required|integer',
            'no_of_owner' => ['sometimes', 'required', 'string', Rule::in(CarNoOfOwner::allTypes())],
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric',
        ];
    }
}
