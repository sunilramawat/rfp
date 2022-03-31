<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteThemOut extends Model
{
    protected $table = "vote_them_outs";
    protected $primaryKey = 'id';

    public $timestamps = false;
}
