<?php

namespace App\Traits\Comment;


use Auth;
use App\Models\User;
use App\Models\Comments\Comment;

trait HasComments
{
    public function comments() {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderByDesc('id');
    }

    /**
     * Get the lastets of the model's Comment
     * @return mixed
     */
    public function latestComment()
    {
        return $this->morphOne(Comment::class, 'commentable')->latest('id');
    }

    /**
     * Get the oldest of the model's Comment
     * @return mixed
     */
    public function oldestComment()
    {
        return $this->morphOne(Comment::class, 'commentable')->oldest('id');
    }

    #region Custom Functions

    public function addComment(User $author, $comment_text) {
        if (empty($comment_text)) {
            return false;
        }

        $comment = $this->comments()->create([
            'comment_text' => $comment_text,
        ])
        ->author()->associate($author);
        $comment->save();

        return $comment;
    }

    public function removeComments() {
        $this->comments()->each(function($comment) {
            $comment->delete();
        });
    }

    #endregion

    protected function initializeHasComments()
    {
        $this->with = array_unique(array_merge($this->with, ['comments']));
    }

    public static function bootHasComments()
    {
        static::deleting(function ($model) {
            $model->removeComments();
        });
    }
}
