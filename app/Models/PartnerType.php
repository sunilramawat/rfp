<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerType extends Model
{
    protected $table = "partner_types";
     public $timestamps = false;
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];
}
