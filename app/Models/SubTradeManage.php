<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TradeManage;
class SubTradeManage extends Model
{

    protected $table = "sub_trade_manage";
    public $timestamps = false;
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
        'trade_id',
        'sub_trade',
        'created_at',
        'modify_at',

    ];
    
    public function TradeManage() {
        return $this->belongsTo(TradeManage::class,'trade_id','id')->where('trade_manage.soft_delete', '=', 0);
    }
   /* public function SubTrader()
    {
        return $this->belongsTo(TradeManage::class,'trade_id');
    }*/


    
}
