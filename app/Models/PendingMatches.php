<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingMatches extends Model
{

    protected $table = "pending_matches";
    public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'sender_id',
        'reciver_id',
        'cat_id',
        'sub_cat_id',
        'added_date',
    ];
    
}
