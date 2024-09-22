<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginUserRequest extends FormRequest
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
            'phoneNumber' => [
                'required',
                'regex:/^[0-9]{10}$/', // exactly 10 digits
            ],
            'otp' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9]{4}$/' // exactly 4 alphanumeric characters
            ]
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages()
    {
        return [
            'phoneNumber.regex' => 'The phone number must be a valid 10-digit number.',
            'otp.regex' => 'The OTP must be exactly 4 characters long and alphanumeric.'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
