<?php

namespace App\Http\Requests\HowTo;

use App\Models\HowTos\HowTo;

class UpdateHowToRequest extends HowToRequest
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
        return HowTo::updateRules($this->howto);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'howtotype' => $this->setRelevantHowToType( $this->input('howtotype'), 'id', true ),
            'tags' => $this->getTagsAsAray( $this->decodeJsonField( $this->input('tags') ) ),
        ]);
    }
}
