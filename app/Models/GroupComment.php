<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupComment extends Model
{
    protected $table = "group_comments";
     protected $primaryKey = 'c_id';

    public $timestamps = false;
}
