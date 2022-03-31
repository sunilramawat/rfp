<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{

    protected $table = "order_payout";
    public $timestamps = false;

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'supplier_id',
        'order_id',
        'no_of_order',
        'total_amount',
        'payment_status',
        'last_payout_date',
        'last_payout_time',
    ];
    
}
