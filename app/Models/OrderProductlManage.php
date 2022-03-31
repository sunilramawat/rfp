<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProductlManage extends Model
{

    protected $table = "orders_products_manage";
    public $timestamps = false;

    public function Order() {
        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function ProductManage() {
        return $this->hasMany(ProductManage::class, 'id','product_id');
    }
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'product_id',
        'product_detail_id',
        'quantity',
        'per_unit_price',
        'total_amount',
        ];
    
}
