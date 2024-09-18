<?php

namespace App\Http\Requests\HowTo;

use App\Models\HowTos\HowTo;
use App\Models\HowTos\HowToType;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class HowToRequest
 * @package App\Http\Requests\HowTo
 *
 * @property string $title
 * @property string|null $view
 * @property string $description
 * @property string $code
 * @property mixed $tags
 *
 * @property-read HowTo $howto
 * @property-read HowToType|null $howtotype
 */

class HowToRequest extends FormRequest
{
    use RequestTraits;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return HowTo::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return HowTo::messagesRules();
    }
}
