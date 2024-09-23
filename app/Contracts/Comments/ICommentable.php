<?php

namespace App\Contracts\Comments;

use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

interface ICommentable extends Auditable
{
    public function addComment(User $author, $comment_text);
    public function removeComments();
    
}
