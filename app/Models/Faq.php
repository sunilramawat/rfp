<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    protected $table = "faqs";

    public $timestamps = false;

   
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'question',
        'f_showing_order',
        'f_status',
        
    ];




    
}
