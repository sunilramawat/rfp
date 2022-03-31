<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $table = "forums";
    protected $primaryKey = 'id';

    public $timestamps = false;
}
