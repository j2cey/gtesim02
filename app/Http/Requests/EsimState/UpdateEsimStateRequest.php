<?php

namespace App\Http\Requests\EsimState;

use App\Models\Esims\EsimState;

/**
 * Class UpdateEsimStateRequest
 * @package App\Http\Requests\EsimState
 *
 * @property EsimState $esimstate
 */

class UpdateEsimStateRequest extends EsimStateRequest
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
        return EsimState::updateRules($this->esimstate);
    }
}
