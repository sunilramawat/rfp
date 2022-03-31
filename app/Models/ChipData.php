<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChipData extends Model
{

    protected $table = "chip_datas";
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'c_id',
        'unique_id',
        'u_id',
        'cycle_count',
        'data_date_time',
        'updated_at',
        'created_at',
    ];
    
}
