<?php

namespace App\Http\Requests;

use App\Enums\CarBrand;
use App\Enums\CarFuelType;
use App\Enums\CarNoOfOwner;
use App\Enums\CarTransmission;
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
            'brand' => ['required', 'string', Rule::in(CarBrand::allTypes())],
            'year' => 'required|digits:4',
            'fuelType' => ['required', 'string', Rule::in(CarFuelType::allTypes())],
            'transmission' => ['required', 'string', Rule::in(CarTransmission::allTypes())],
            'kmDriven' => 'required|integer',
            'owners' => ['required', 'string', Rule::in(CarNoOfOwner::allTypes())],
            'description' => 'required|string',
        ];
    }
}
