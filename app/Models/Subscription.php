<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = "subscriptions";
    public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'country',
        'country',
        'name',
        'itunes_product_id',
        'android_product_id',
        'android_product_id',
        'price',
        'valid_for_days',
        'discription',
        
    ];
}
