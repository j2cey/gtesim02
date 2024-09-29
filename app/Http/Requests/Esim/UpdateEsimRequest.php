<?php

namespace App\Http\Requests\Esim;

use App\Models\Esims\Esim;
use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateEsimRequest Class
 *
 * @property Esim $esim
 */
class UpdateEsimRequest extends EsimRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return Esim::updateRules($this->esim);
    }
}
