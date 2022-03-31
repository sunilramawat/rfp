<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    public $timestamps = false;
    protected $table = "partners";
	/**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'region',
        'desc',
        'type_text',
        'type',
        'location',
        'opening',
        'closing',
        'suitable',
        'event_type',
        'is_recommend',
        'category',
        'sub_category',
        'is_premium',
        'promo_code',
        'promo_detail',
        'status',
        'photo',
        'is_discount',
    ];
    
}
