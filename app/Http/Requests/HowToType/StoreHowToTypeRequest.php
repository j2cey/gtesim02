<?php

namespace App\Http\Requests\HowToType;

use App\Models\HowTos\HowToType;
use Illuminate\Foundation\Http\FormRequest;

class StoreHowToTypeRequest extends FormRequest
{
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
        return HowToType::createRules();
    }
}
