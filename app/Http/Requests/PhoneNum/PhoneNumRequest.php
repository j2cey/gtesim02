<?php

namespace App\Http\Requests\PhoneNum;

use App\Models\Person\PhoneNum;
use App\Traits\Request\RequestTraits;
use App\Contracts\Persons\IHasPhoneNums;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PhoneNumRequest
 * @package App\Http\Requests\PhoneNum
 *
 * @property string $phone_number
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property integer|null $esim_id
 *
 * @property PhoneNum $phonenum
 * @property IHasPhoneNums $hasphonenum
 */
class PhoneNumRequest extends FormRequest
{
    use RequestTraits;

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
        return PhoneNum::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return PhoneNum::messagesRules($this->hasphonenum);
    }
}
