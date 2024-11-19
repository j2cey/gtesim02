<?php

namespace App\Http\Requests\Employe;

use App\Models\Employes\Employe;
use App\Models\Person\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class AddEmailAddressClientEsimRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property string $email_address
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
 * @property Employe $employe
 */
class AddEmployeEmailaddressRequest extends FormRequest
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
            'email_address' => array_merge(EmailAddress::createRules($this->email_address, Employe::class)['email_address'], []),
            //'model_selected'=> ['required',],
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
            'email_address.required' => 'Adresse Mail requise',
            'email_address.email' => 'E-Mail non valide',
            'model_selected.required' => 'Model requis',
        ];
    }
}
