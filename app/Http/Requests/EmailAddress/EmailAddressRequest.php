<?php

namespace App\Http\Requests\EmailAddress;

use App\Models\Person\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\Persons\IHasEmailAddresses;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class PhoneNumRequest
 * @package App\Http\Requests\PhoneNum
 *
 * @property string $email_address
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
 *
 * @property EmailAddress $emailaddress
 * @property IHasEmailAddresses $hasemailaddress
 */
class EmailAddressRequest extends FormRequest
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
        return EmailAddress::defaultRules();
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
}
