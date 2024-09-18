<?php

namespace App\Http\Requests\Permission;

use App\Models\Authorization\Permission;

class UpdatePermissionRequest extends PermissionRequest
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
        return Permission::updateRules($this->permission);
    }
}
