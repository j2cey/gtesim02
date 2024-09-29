<?php

namespace App\Http\Requests\ClientEsim;

use App\Models\Esims\ClientEsim;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClientEsimRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property string $nom_raison_sociale
 * @property string $prenom
 * @property string $email_address
 * @property string $phone_number
 * @property string $pin
 * @property string $puk
 *
 * @property integer|null $esim_id
 */

class ClientEsimRequest extends FormRequest
{
    use RequestTraits;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return ClientEsim::defaultRules($this->phone_number);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return ClientEsim::messagesRules();
    }
}
