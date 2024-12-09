<?php

namespace App\Http\Requests\Status;

use App\Models\Status;
use App\Contracts\IsBaseModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class StatusChangeRequest
 * @package App\Http\Requests\Status
 *
 * @property string $statuscode
 * @property string $modelclass
 * @property string $modeltype
 * @property string $modelid
 * @property Status $status
 *
 * @property IsBaseModel $model
 */
class StatusChangeRequest extends FormRequest
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
        return [
            'statuscode' => ['required'],
            'modelclass' => ['required'],
            'modeltype' => ['required'],
            'modelid' => ['required'],
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
            'status' => Status::getByCode($this->input('statuscode')),
            'model' => $this->input('modelclass')::getByUuid($this->input('modelid')),
        ]);
    }
}
