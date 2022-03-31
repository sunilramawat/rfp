<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = "groups";
    protected $primaryKey = 'g_id';

    public $timestamps = false;
}
