<?php

namespace App\Http\Requests\Role;

use App\Models\Authorization\Role;

class StoreRoleRequest extends RoleRequest
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
        return Role::createRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'permissions' => $this->setRelevantIdsList($this->input('permissions'), false),
        ]);
    }
}
