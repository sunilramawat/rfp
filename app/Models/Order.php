<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = "order_manage";
    public $timestamps = false;

    public function OrderProductlManage() {
        return $this->hasMany(OrderProductlManage::class, 'order_id', 'id');
    }
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'order_code',
        'user_id',
        'supplier_id',
        'order_date',
        'total_amount',
        'order_status',
    ];
    
}
