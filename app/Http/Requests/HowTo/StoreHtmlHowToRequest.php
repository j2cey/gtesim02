<?php

namespace App\Http\Requests\HowTo;

use App\Models\HowTos\HowTo;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreHtmlHowToRequest
 * @package App\Http\Requests\HowTo
 *
 * @property string $htmlbody
 * @property int $howto
 * @property array $images
 */
class StoreHtmlHowToRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
