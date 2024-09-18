<?php

namespace App\Http\Requests\ClientEsim;

use Illuminate\Validation\Rule;
use App\Models\Esims\ClientEsim;

/**
 * Class StoreClientEsimRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property ClientEsim $client_matched_selected
 */

class StoreClientEsimRequest extends ClientEsimRequest
{
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
        return ClientEsim::createRules($this->numero);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'client_matched_selected' => is_null($this->input('client_matched_selected')) ? null : $this->setRelevantClientEsim( ['uuid' => $this->input('client_matched_selected')], 'uuid'),
        ]);
    }
}
