<?php

namespace App\Http\Requests\Status;

use App\Models\Status;
use App\Enums\Auth\Permissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 *
 * @property Status $status
 */
class UpdateStatusRequest extends StatusRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can( Permissions::Status()->update() );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return Status::updateRules($this->status);
    }
}
