<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;
    protected $table = "reports";

	/**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'photo_id',
        'reported_userid',
        'report_type',
        'report_desc',
    ];
    
}
