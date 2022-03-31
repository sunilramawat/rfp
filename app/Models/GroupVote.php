<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupVote extends Model
{
    protected $table = "group_votes";
     protected $primaryKey = 'v_id';

    public $timestamps = false;
}
