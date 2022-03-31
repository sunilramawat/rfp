<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable;
    //protected $table = "users";
	/**
     * Fillable.
     *
     * @var array
     */
    /*protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'device_id',
        'device_type',
        'user_type',
        'user_status',
        'added_date',
    ];*/

    protected $fillable = [
        'name', 'email', 'password',
    ];
    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
        'password', 'remember_token',
    ];

   

    public function expiredToken(){

        return $this->hasMany('\App\OauthAccessToken');
    }
    
}
