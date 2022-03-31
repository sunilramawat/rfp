<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debet extends Model
{
    protected $table = "debets";
    protected $primaryKey = 'id';

    public $timestamps = false;
}
