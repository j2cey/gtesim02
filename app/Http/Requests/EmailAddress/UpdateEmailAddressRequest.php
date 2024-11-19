<?php

namespace App\Http\Requests\EmailAddress;

use App\Models\Person\EmailAddress;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateEmailAddressRequest extends EmailAddressRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return EmailAddress::updateRules($this->emailaddress,$this->email_address,$this->hasemailaddress_type);
    }
}
