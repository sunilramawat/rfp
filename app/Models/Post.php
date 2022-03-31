<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $table = "posts";

    public $timestamps = false;
  
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'p_id',
        'imgUrl',
        'p_u_id',
        'post_type',
        'description',
        'like_count',
        'favourite_count',
        'comment_count',
        'share_count',
        'retweet_count',
        'posted_time',
        'stock_name',
        'stock_target_price',
        'time_left',
        'term',
        'result',
        'total_votes',
        'trend',
        'recommendation',
    ];




    
}
