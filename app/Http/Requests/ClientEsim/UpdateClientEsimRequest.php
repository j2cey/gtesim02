<?php

namespace App\Http\Requests\ClientEsim;

use App\Models\Esims\ClientEsim;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientEsimRequest extends ClientEsimRequest
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
        return ClientEsim::updateRules($this->clientesim,$this->phonenum);
    }
}
