<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $table = "forum_topics";
    protected $primaryKey = 't_id';

    public $timestamps = false;
}
