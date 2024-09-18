<?php

namespace App\Http\Requests\StatutEsim;

use App\Models\Esims\Esim;
use App\Models\Esims\StatutEsim;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ModelUpdateStatutEsimRequest
 * @package App\Http\Requests\StatutEsim
 *
 * @property StatutEsim $status
 * @property Esim $model
 */

class ModelUpdateStatutEsimRequest extends FormRequest
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
        return [];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->setRelevantStatutEsim($this->input('statutesim'),'id'),
            'model' => $this->setRelevantPolymorph($this->input('model_type'),$this->input('model_id')),
        ]);
    }
}
