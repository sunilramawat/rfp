<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{

    protected $table = "user_answers";

    public $timestamps = false;

   
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'q_id',
        'a_id',
        'u_id',
        
    ];




    
}
