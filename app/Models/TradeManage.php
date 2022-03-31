<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeManage extends Model
{

    protected $table = "trade_manage";

    public $timestamps = false;

   /* public function SubTradeManage()
    {
        return $this->hasMany(SubTradeManage::class);
    }*/
    public function SubTradeManage() {
        return $this->hasMany(SubTradeManage::class, 'trade_id', 'id')->where('sub_trade_manage.soft_delete', '=', 0);
    }

    public function ProductManage() {
        return $this->hasMany(ProductManage::class, 'trade_id','id');
    }
    // ...
    // boot
    /*static::creating( function ($model) {
        $model->setCreatedAt($model->freshTimestamp());
    });*/
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'trade',
        'trade_logo',
        'block_status',
        'created_at',
        'modify_at',

    ];




    
}
