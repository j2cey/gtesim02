<?php

namespace App\Http\Requests\Employe;

use App\Models\Employes\Employe;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UpdateEmployeRequest
 * @package App\Http\Requests\Employe
 *
 * @property Employe $employe
 */
class UpdateEmployeRequest extends EmployeRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return Employe::updateRules($this->employe);
    }
}
