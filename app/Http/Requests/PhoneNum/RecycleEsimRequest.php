<?php

namespace App\Http\Requests\PhoneNum;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RecycleEsimRequest
 * @package App\Http\Requests\PhoneNum
 *
 * @property integer|null $esim_id
 */
class RecycleEsimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
}
