<?php

namespace App\Http\Requests;

use App\Enums\CategoryGuardName;
use App\Enums\PostType;
use App\Enums\PropertyFacing;
use App\Enums\PropertyListedBy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostLandPlotRequest extends FormRequest
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
            'listedBy' => ['required', 'string', Rule::in(PropertyListedBy::allTypes())],
            'plotArea' => 'required|integer',
            'length' => 'nullable|integer',
            'breadth' => 'nullable|integer',
            'facing' => ['required', 'string', Rule::in(PropertyFacing::allTypes())],
            'projectName' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }
}
