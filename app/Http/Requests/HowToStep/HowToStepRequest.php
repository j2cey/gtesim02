<?php

namespace App\Http\Requests\HowToStep;

use App\Models\HowTos\HowTo;
use App\Models\HowTos\HowToStep;
use App\Models\HowTos\HowToThread;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class HowToStepRequest
 * @package App\Http\Requests\HowToStep
 *
 * @property string $title
 * @property string $posi
 * @property string $description
 *
 * @property HowToThread $howtothread
 * @property HowTo $howto
 * @property HowToStep $howtostep
 */

class HowToStepRequest extends FormRequest
{
    use RequestTraits;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return HowToStep::defaultRules();
    }
}
