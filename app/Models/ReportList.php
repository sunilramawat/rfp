<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportList extends Model
{
    public $timestamps = false;
    protected $table = "report_lists";
	/**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'report',
       
    ];
    
}
