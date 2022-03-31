<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteThemOutComment extends Model
{
    protected $table = "vote_them_out_comments";
     protected $primaryKey = 'c_id';

    public $timestamps = false;
}
