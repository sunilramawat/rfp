<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = "rooms";
    protected $primaryKey = 'r_id';

    public $timestamps = false;
}
