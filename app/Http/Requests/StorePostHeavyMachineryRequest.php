<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\Condition;
use App\Enums\HeavyMachineryBrand;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostHeavyMachineryRequest extends FormRequest
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
            'adTitle' => 'required|string|max:255',
            'brand' => ['required', 'string', Rule::in(HeavyMachineryBrand::allTypes())],
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4',
            'condition' => ['required', 'string', Rule::in(Condition::allTypes())],
            'owners' => 'required|integer',
            'listedBy' => 'required|string',
            'fuelType' => ['required', 'string', Rule::in(CarFuelType::allTypes())],
            // 'hours_used' => 'required|integer',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
        ];
    }
}
