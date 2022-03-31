<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialities extends Model
{

    protected $table = "specialities";
	/**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        's_id',
        's_name',
        'status',
    ];
    
}
