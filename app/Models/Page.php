<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{

    protected $table = "pages";

    public $timestamps = false;

   
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'p_title',
        'p_description',
        'p_status',
        
    ];




    
}
