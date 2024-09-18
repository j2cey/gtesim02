<?php

namespace App\Http\Requests\Comment;

use App\Models\Comments\Comment;

class StoreCommentRequest extends CommentRequest
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
        return Comment::createRules();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'commentable' => $this->setRelevantPolymorph( $this->input('commentable_type'), $this->input('commentable_id') ),
            'author' => auth()->user(),
        ]);
    }
}
