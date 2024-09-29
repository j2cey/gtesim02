<?php

namespace App\Http\Requests\ClientEsim;

use App\Models\Esims\ClientEsim;
use App\Models\Employes\PhoneNum;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreClientEsimPhonenumRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property ClientEsim $clientesim
 * @property PhoneNum $phonenum
 */
class UpdateClientEsimPhoneNumRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return PhoneNum::updateRules($this->phonenum, $this->phonenum, $this->hasphonenum_type);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return PhoneNum::messagesRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'hasphonenum_type' => ClientEsim::class,
            'hasphonenum_id' => $this->clientesim->id,
        ]);
    }
}
