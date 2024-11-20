<?php

namespace App\Http\Requests\ArisStatus;

use App\Models\Aris\ArisStatus;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class StoreArisStatusRequest
 *
 * @package App\Http\Requests\ArisStatus
 *
 * @property string|null $iccid
 *
 */
class StoreArisStatusRequest extends ArisStatusRequest
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
        return ArisStatus::createRules();
    }
}
