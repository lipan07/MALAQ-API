<?php

namespace App\Http\Requests;

use App\Enums\PositionType;
use App\Enums\SalaryPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostJobRequest extends FormRequest
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
            'salary_period' => ['sometimes', 'required', 'string', Rule::in(SalaryPeriod::allTypes())],
            'position_type' => ['sometimes', 'required', 'string', Rule::in(PositionType::allTypes())],
            'salary_from' => 'sometimes|required|numeric',
            'salary_to' => 'sometimes|nullable|numeric',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ];
    }
}
