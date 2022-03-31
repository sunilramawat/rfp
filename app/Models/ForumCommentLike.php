<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumCommentLike extends Model
{
    protected $table = "forum_comment_likes";

    public $timestamps = false;
}
