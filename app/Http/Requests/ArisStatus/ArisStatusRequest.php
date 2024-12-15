<?php

namespace App\Http\Requests\ArisStatus;

use Illuminate\Support\Carbon;
use App\Models\Aris\ArisStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ArisStatusRequest
 *
 * @package App\Http\Requests\ArisStatus
 *
 * @property string|null $icc
 * @property string|null $status
 * @property Carbon|null $status_change_date
 * @property Carbon|null $requested_at
 * @property Carbon|null $responded_at
 * @property string|null $response_message
 * @property string|null $request_id
 */
class ArisStatusRequest extends FormRequest
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
        return ArisStatus::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return ArisStatus::messagesRules();
    }
}
