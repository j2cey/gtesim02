<?php

namespace App\Models\Comments;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Comment
 * @package App\Models\Comments
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 *
 * @property string $comment_text
 * @property integer $votes
 * @property integer $spam
 * @property integer $reply_id
 * @property integer $page_id
 * @property integer $user_id
 *
 * @property string $commentable_type
 * @property integer $commentable_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

class Comment extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $with = ['author'];

    #region Validation Rules

    public static function defaultRules() {
        return [
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(),
            [
                'comment_text' => ['required'],
                'reply_id' => 'filled',
                'page_id' => 'filled',
                'author' => 'required',
            ]
        );
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(),
            []
        );
    }

    public static function messagesRules() {
        return [
        ];
    }

    #endregion

    #region Eloquent Relationships

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(__CLASS__,'id','reply_id');
    }

    #endregion

    #region Custom Functions

    public static function createNew()
    {
    }

    public function updateOne()
    {
    }

    #endregion
}
