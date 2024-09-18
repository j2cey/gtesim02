<?php

namespace App\Http\Requests\PhoneNum;

use App\Models\Employes\PhoneNum;

class UpdatePhoneNumRequest extends PhoneNumRequest
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
        return PhoneNum::updateRules($this->phonenum,$this->numero,$this->hasphonenum_type);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'hasphonenum' => $this->setRelevantPolymorph( $this->input('hasphonenum_type'), $this->input('hasphonenum_id') ),
        ]);
    }
}
