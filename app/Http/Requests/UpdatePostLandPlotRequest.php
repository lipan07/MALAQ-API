<?php

namespace App\Http\Requests;

use App\Enums\PropertyFacing;
use App\Enums\PropertyListedBy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostLandPlotRequest extends FormRequest
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
            'listed_by' => ['sometimes', 'required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'carpet_area' => 'sometimes|required|integer',
            'length' => 'sometimes|nullable|integer',
            'breadth' => 'sometimes|nullable|integer',
            'facing' => ['sometimes', 'required', 'string', Rule::in(PropertyFacing::allTypes())],
            'project_name' => 'sometimes|nullable|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric',
        ];
    }
}
