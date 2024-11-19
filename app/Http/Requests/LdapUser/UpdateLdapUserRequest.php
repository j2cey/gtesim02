<?php

namespace App\Http\Requests\LdapUser;

use App\Models\LdapUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * UpdateLdapUserRequest Class
 *
 * @property LdapUser $ldapuser
 */
class UpdateLdapUserRequest extends LdapUserRequest
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
        return LdapUser::updateRules($this->ldapuser);
    }
}
