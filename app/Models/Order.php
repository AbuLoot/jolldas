<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'order_id');
    }

    public function products()
    {
    	return $this->belongsToMany('App\Models\Product', 'product_order');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }
}
