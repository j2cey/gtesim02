<?php

namespace App\Http\Requests\Tag;

use App\Models\BaseModel;
use LdapRecord\Models\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class SyncTagsRequest
 * @package App\Http\Requests\Tag
 *
 * @property string $modelclass
 * @property string $modelid
 * @property array $relevanttags
 *
 * @property BaseModel|Model|mixed $model
 */
class SyncTagsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'modelclass' => ['required'],
            'modelid' => ['required'],
            'relevanttags' => ['required'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'model' => $this->input('modelclass')::getByUuid($this->input('modelid')),
        ]);
    }
}
