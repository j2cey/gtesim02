<?php

namespace App\Http\Requests\Esim;

use App\Models\Esims\Esim;
use App\Models\Esims\StatutEsim;
use App\Models\Esims\TechnologieEsim;
use Illuminate\Foundation\Http\FormRequest;

/**
 * EsimRequest Class
 *
 * @property string $imsi
 * @property string $iccid
 * @property string $ac
 * @property string $pin
 * @property string $puk
 * @property string $eki
 * @property string $pin2
 * @property string $puk2
 * @property string $adm1
 * @property string $opc
 *
 * @property StatutEsim $statutesim
 * @property TechnologieEsim $technologieesim
 */
class EsimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return Esim::messagesRules();
    }
}
