<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UserRequest
 * @package App\Http\Requests\User
 *
 * @property string $name
 * @property string $login
 * @property string $email
 *
 * @property string $password
 *
 * @property string|null $avatar
 * @property boolean $is_local
 * @property boolean $is_ldap
 *
 * @property User $user
 */
class UserRequest extends FormRequest
{
    use RequestTraits;

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
