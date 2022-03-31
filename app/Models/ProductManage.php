<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductManage extends Model
{

    protected $table = "products_manage";
    public $timestamps = false;

    public function OrderProductManage() {
        return $this->belongsTo(OrderProductManage::class,'product_id','id');
    }

    public function ProductDetailManage() {
        return $this->hasMany(ProductDetailManage::class, 'product_id', 'id');
    }

    public function TradeManage() {
        return $this->belongsTo(TradeManage::class,'id','trade_id');
    }
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'product_name',
        'product_image_url',
        'trade_id',
        'brief_description',
        'delivery_option',
        'supplier_type',
        'block_status',
        'sub_trade_id',
        'rating',
        'review_count',
        'soft_delete',
    ];
    
}
