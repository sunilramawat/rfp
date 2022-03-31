<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuthority extends Model
{

    protected $table = "jhi_user_authority";

    public $timestamps = false;

   /* public function SubTradeManage()
    {
        return $this->hasMany(SubTradeManage::class);
    }*/
   /* public function User() {
        return $this->hasMany(User::class, 'trade_id', 'id');
    }*/
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
    /*protected $fillable = [
        'id',
        'trade',
        'trade_logo',
        'block_status',
        'created_at',
        'modify_at',

    ];*/




    
}
