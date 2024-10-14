<?php

namespace App\Http\Requests\User;


use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends UserRequest
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
        return User::createRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_local' => $this->setCheckOrOptionValue($this->input('is_local')),
            'is_ldap' => $this->setCheckOrOptionValue($this->input('is_ldap')),
        ]);
    }
}
