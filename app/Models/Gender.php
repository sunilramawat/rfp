<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = "genders";
     public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'gender',
        'status',
        
    ];
}
