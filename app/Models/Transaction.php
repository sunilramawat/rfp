<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";
    public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'device_type',
        'subscription_id',
        'total_amount',
        'itunes_receipt',
        'itune_original_transaction_id',
        'orderId',
        'packageName',
        'productId',
        'purchaseTime',
        'purchaseState',
        'purchaseToken',
        'payment_status',
        'failure_message',
        
    ];
}
