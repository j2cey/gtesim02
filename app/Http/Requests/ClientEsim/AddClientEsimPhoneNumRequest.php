<?php

namespace App\Http\Requests\ClientEsim;

use App\Models\Person\PhoneNum;
use App\Models\Esims\ClientEsim;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AddPhoneClientEsimRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property string phone_number
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property integer|null $esim_id
 * @property ClientEsim $clientesim
 */
class AddClientEsimPhoneNumRequest extends FormRequest
{
    use RequestTraits;

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
        return [
            'phone_number' => array_merge(PhoneNum::createRules($this->phone_number, ClientEsim::class)['phone_number'], ['starts_with:060,065,066']),
            'model_selected'=> ['required',],
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
            'phone_number.required' => 'Numéro de téléphone requis',
            'phone_number.regex' => 'Numéro de téléphone non valide',
            'phone_number.min' => 'Numéro de téléphone doit avoir 8 digits minimum',
            'phone_number.unique' => 'Numéro déjà attribué',
            'model_selected.required' => 'Veuillez sélectionner un Client',
            'phone_number.starts_with' => 'Le numéro de téléphone doit commencer par 060,065,066',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'clientesim' => is_null($this->input('model_selected')) ? null : $this->setRelevantClientEsim( ['uuid' => $this->input('model_selected')], 'uuid'),
        ]);
    }
}
