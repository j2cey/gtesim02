<?php

namespace App\Http\Requests\HowToStep;

use App\Models\HowTos\HowToStep;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHowToStepRequest extends HowToStepRequest
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
        return HowToStep::updateRules($this->howtostep);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'howtothread' => $this->setRelevantHowToThread( $this->input('howtothread'), "id", true ),
            'howto' => $this->setRelevantHowTo( $this->input('howto'), "id", true ),
            'posi' => (int)$this->input('posi'),
        ]);
    }
}
