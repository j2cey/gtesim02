<?php

namespace App\Http\Requests\ClientEsim;

use Illuminate\Validation\Rule;
use App\Enums\Auth\Permissions;
use App\Models\Esims\ClientEsim;
use Illuminate\Support\Facades\Auth;

/**
 * Class StoreClientEsimRequest
 * @package App\Http\Requests\ClientEsim
 *
 * @property ClientEsim $model_selected
 */

class StoreClientEsimRequest extends ClientEsimRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can( Permissions::ClientEsim()->create() );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ClientEsim::createRules($this->phone_number);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'model_selected' => is_null($this->input('model_selected')) ? null : $this->setRelevantClientEsim( ['uuid' => $this->input('model_selected')], 'uuid'),
        ]);
    }
}
