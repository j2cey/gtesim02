<?php

namespace App\Http\Requests\Esim;

use App\Models\Esims\Esim;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property \stdClass $statutesim
 * @property \stdClass $technologieesim

 */

class StoreEsimRequest extends EsimRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
    public function rules(): array
    {
        return Esim::createRules();
    }

    public function prepareForValidation()
    {
        //dd($this);
    }
}
