<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PositionType;
use App\Enums\PostType;
use App\Enums\SalaryPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostJobRequest extends FormRequest
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
            'salaryPeriod' => ['required', 'string', Rule::in(SalaryPeriod::allTypes())],
            'positionType' => ['required', 'string', Rule::in(PositionType::allTypes())],
            'description' => 'required|string',
        ];
    }
}
