<?php

namespace App\Http\Requests\ClientEsim;

use App\Models\Esims\ClientEsim;
use App\Models\Person\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UpdateClientEsimEmailAddressRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property string $email_address
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
 * @property ClientEsim $clientesim
 * @property EmailAddress $emailaddress
 */
class UpdateClientEsimEmailAddressRequest extends FormRequest
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
        return EmailAddress::updateRules($this->emailaddress, $this->email_address, $this->hasemailaddress_type);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return EmailAddress::messagesRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'hasemailaddress_type' => ClientEsim::class,
            'hasemailaddress_id' => $this->clientesim->id,
        ]);
    }
}
