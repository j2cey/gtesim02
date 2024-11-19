<?php

namespace App\Http\Requests\LdapUser;

use App\Models\LdapUser;
use App\Models\Employes\Departement;
use App\Models\Employes\FonctionEmploye;
use Illuminate\Foundation\Http\FormRequest;

/**
 * LdapUserRequest Class
 *
 * @property string $guid
 * @property string $firstname
 * @property string $lastname
 * @property string $login
 * @property string $email
 * @property string $domain
 * @property string $telephone
 * @property string $distinguishedname
 *
 * @property Departement $department
 * @property FonctionEmploye $fonction
 */
class LdapUserRequest extends FormRequest
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
        return LdapUser::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return LdapUser::messagesRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'department' => is_null($this->input('department')) ? null : Departement::getByUuid($this->input('department')['uuid']),
            'fonction' => is_null($this->input('fonction')) ? null : FonctionEmploye::getByUuid($this->input('fonction')['uuid']),
        ]);
    }
}
