<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image_url', 'categories_id','description', 'sell_price', 'buy_price', 'currency', 'weight', 'is_hidden','stock', 'position', 'x', 'y', 'decoration_position_id', 'store_id', 'discount', 'discount_expired_at'
    ];
}
