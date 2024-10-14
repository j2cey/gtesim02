<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ResetPasswordRequest
 * @package App\Http\Requests\User
 *
 * @property string newpwd
 */
class ResetPasswordRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'newpwd' => ['required',],
            'renewpwd' => ['required','same:newpwd'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'newpwd.required' => 'The New Pasword is Required',
            'renewpwd.required' => 'Please confirm the New Password',
            'renewpwd.same' => 'Please confirm with same Password',
        ];
    }
}
