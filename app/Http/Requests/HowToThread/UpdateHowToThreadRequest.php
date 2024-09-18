<?php

namespace App\Http\Requests\HowToThread;

use App\Models\HowTos\HowToThread;

class UpdateHowToThreadRequest extends HowToThreadRequest
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
        return HowToThread::updateRules($this->howtothread);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tags' => $this->getTagsAsAray( $this->decodeJsonField( $this->input('tags') ) ),
        ]);
    }
}
