<?php

namespace App\Http\Requests\Comment;

use App\Models\Comments\Comment;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCommentRequest
 * @package App\Http\Requests\Comment
 *
 * @property string $update_type
 * @property string $vote
 * @property integer $users_id
 */

class UpdateCommentRequest extends CommentRequest
{
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
        return Comment::updateRules($this->comment);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'commentable' => $this->setRelevantPolymorph( $this->input('commentable_type'), $this->input('commentable_id') ),
            'author' => $this->setRelevantUser($this->input('author'), true),
        ]);
    }
}
