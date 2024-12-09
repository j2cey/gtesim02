<?php

namespace App\Http\Requests\HowToThread;

use App\Models\HowTos\HowToThread;
use App\Traits\Request\RequestTraits;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class HowToThreadRequest
 * @package App\Http\Requests\HowToThread
 *
 * @property string $title
 * @property string $code
 * @property array $image
 *
 * @property string $description
 *
 * @property mixed $tags
 * @property HowToThread $howtothread
 */

class HowToThreadRequest extends FormRequest
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
        return HowToThread::defaultRules();
    }
}
