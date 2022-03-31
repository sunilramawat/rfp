<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierManage extends Model
{

    protected $table = "suppliers_manage";
    public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'supplier_name',
        'owner_name',
        'logo_url',
        'email',
        'phone_code',
        'phone_number',
        'location',
        'supplier_type',
        'block_status',
        'approve_status',
        'modify_at',
        'created_at',
        'member_user_id',
        'latitude',
        'longgitude',
    ];
    
}
