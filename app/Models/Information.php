<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Information extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'informations';
    protected $fillable = [
        'short_description', 'description', 'no_telp', 'email', 'link_market_place', 'link_tiktok', 'link_instagram', 'link_facebook', 'link_twitter', 'link_pinterest', 'link_linkedin', 'link_youtube', 'city', 'address'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
