<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetailManage extends Model
{

    protected $table = "products_detail_manage";
    public $timestamps = false;

     public function ProductManage() {
        return $this->belongsTo(ProductManage::class,'product_id','id');
    }

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'product_id',
        'quantity',
        'size',
        'size_unit',
        'price_per_unit',
        'dimension',
        ];
    
}
