<?php

namespace App\Http\Requests\Comment;

use App\Models\User;
use App\Models\Comments\Comment;
use App\Traits\Request\RequestTraits;
use App\Contracts\Comments\ICommentable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentRequest
 * @package App\Http\Requests\Comment
 *
 * @property string $comment_text
 * @property integer $votes
 * @property integer $spam
 * @property integer $reply_id
 * @property integer $page_id
 * @property integer $user_id
 *
 * @property User $author
 * @property Comment $comment
 * @property ICommentable $commentable
 */

class CommentRequest extends FormRequest
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
        return Comment::defaultRules();
    }
}
