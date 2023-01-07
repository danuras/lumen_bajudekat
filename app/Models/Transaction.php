<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'cust_id', 'payment','address', 'nomor_resi','kurir', 'total', 'ongkos',
    ];
}
