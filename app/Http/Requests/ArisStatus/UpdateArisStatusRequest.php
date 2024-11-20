<?php

namespace App\Http\Requests\ArisStatus;

use App\Models\Aris\ArisStatus;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UpdateArisStatusRequest
 *
 * @package App\Http\Requests\ArisStatus
 *
 * @property ArisStatus $arisstatus
 */
class UpdateArisStatusRequest extends ArisStatusRequest
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
        return ArisStatus::updateRules($this->arisstatus);
    }
}
