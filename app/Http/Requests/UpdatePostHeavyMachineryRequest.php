<?php

namespace App\Http\Requests;

use App\Enums\CarFuelType;
use App\Enums\Condition;
use App\Enums\HeavyMachineryBrand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostHeavyMachineryRequest extends FormRequest
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
            'brand' => ['required', 'string', Rule::in(HeavyMachineryBrand::allTypes())],
            'condition' => ['required', 'string', Rule::in(Condition::allTypes())],
            'year' => 'required|digits:4',
            'fuelType' => ['required', 'string', Rule::in(CarFuelType::allTypes())],
            'owners' => 'required|integer',
            'listedBy' => 'required|string',
            'adTitle' => 'required|string|max:255',
            // 'hours_used' => 'required|integer',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
        ];
    }
}
