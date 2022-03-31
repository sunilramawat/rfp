<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMessage extends Model
{
    protected $table = "room_msgs";
    protected $primaryKey = 'rm_id';

    public $timestamps = false;
}
