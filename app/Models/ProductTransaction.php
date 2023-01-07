<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransaction extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buy_price', 'sell_price', 'currency', 'amount', 'weight', 'product_id','transaction_id',
    ];
    
}
