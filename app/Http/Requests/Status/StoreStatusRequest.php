<?php

namespace App\Http\Requests\Status;

use App\Models\Status;
use App\Enums\Auth\Permissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreStatusRequest extends StatusRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can( Permissions::Status()->create() );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return Status::createRules();
    }
}
