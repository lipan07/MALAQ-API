<?php

namespace App\Http\Requests;

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
            'title' => 'sometimes|required|string|max:255',
            'brand' => ['sometimes', 'required', 'string', Rule::in(HeavyMachineryBrand::allTypes())],
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|digits:4',
            'condition' => ['sometimes', 'required', 'string', Rule::in(Condition::allTypes())],
            'hours_used' => 'sometimes|required|integer',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric',
            'contact_name' => 'sometimes|required|string|max:255',
            'contact_phone' => 'sometimes|required|string|max:255',
        ];
    }
}
