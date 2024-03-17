<?php

namespace App\Http\Requests;

use App\Enums\CarFuelType;
use App\Enums\CommercialVehicleBrand;
use App\Enums\Condition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostHeavyVehicleRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'brand' => ['sometimes', 'required', 'string', Rule::in(CommercialVehicleBrand::allTypes())],
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|digits:4',
            'condition' => ['sometimes', 'required', 'string', Rule::in(Condition::allTypes())],
            'km_driven' => 'sometimes|required|integer',
            'fuel_type' => ['sometimes', 'required', 'string', Rule::in(CarFuelType::allTypes())],
            'price' => 'sometimes|required|numeric',
            'description' => 'sometimes|nullable|string',
            'contact_name' => 'sometimes|required|string|max:255',
            'contact_phone' => 'sometimes|required|string|max:255',
        ];
    }
}
