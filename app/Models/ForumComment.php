<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = "forum_comments";
     protected $primaryKey = 'c_id';

    public $timestamps = false;
}
