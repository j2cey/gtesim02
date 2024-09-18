<?php

namespace App\Http\Requests\HowToStep;

use App\Models\HowTos\HowToStep;

class StoreHowToStepRequest extends HowToStepRequest
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
        return HowToStep::createRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'howtothread' => $this->setRelevantHowToThread( [ 'id' => $this->input('how_to_thread_id') ] ),
            'howto' => $this->setRelevantHowTo( $this->input('howto') ),
            'posi' => (int)$this->input('posi'),
        ]);
    }
}
