<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    protected $table = "sub_categories";
     public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'sc_id',
        'sc_c_id',
        'sc_name',
        'sc_image',
        'sc_status',
        
    ];
}
