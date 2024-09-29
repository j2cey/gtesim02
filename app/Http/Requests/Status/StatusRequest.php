<?php

namespace App\Http\Requests\Status;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class Status
 * @package App\Models
 *
 * @property string $code
 * @property string $name
 * @property string $style
 * @property string|null $description
 */
class StatusRequest extends FormRequest
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
        return Status::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return Status::messagesRules();
    }
}
