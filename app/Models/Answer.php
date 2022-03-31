<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{

    protected $table = "answers";

    public $timestamps = false;

   
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'answer',
        'u_id',
        'status',
        
    ];




    
}
