<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{

    protected $table = "photos";

    public $timestamps = false;
  
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'p_id',
        'p_photo',
        'p_u_id',
        'is_default',
    ];




    
}
