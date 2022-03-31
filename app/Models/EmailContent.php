<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailContent extends Model
{

    protected $table = "email_contents";
	/**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'ec_id',
        'ec_key',
        'ec_title',
        'ec_from',
        'ec_subject',
        'ec_keywords',
        'ec_message',
        'ec_link_title',
        'ec_status',
    ];
    
}
