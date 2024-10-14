<?php

namespace App\Http\Requests\Employe;

use App\Models\Employes\Employe;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreEmployeRequest
 * @package App\Http\Requests\Employe
 *
 * @property string|null $userid
 */
class StoreEmployeRequest extends EmployeRequest
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
    public function rules()
    {
        return Employe::createRules();
    }
}
