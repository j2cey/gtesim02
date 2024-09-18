<?php

namespace App\Http\Requests\EsimState;

use App\Models\Esims\EsimState;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EsimStateRequest
 * @package App\Http\Requests\EsimState
 *
 * @property integer|null $esim_id
 * @property integer|null $statut_esim_id
 * @property integer|null $user_id
 *
 * @property string|null $details
 */

class EsimStateRequest extends FormRequest
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
        return EsimState::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return EsimState::messagesRules();
    }
}
