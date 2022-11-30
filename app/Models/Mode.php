<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    protected $table = 'modes';

    public $timestamps = false;

    public function products()
    {
    	return $this->belongsToMany('App\Models\Product', 'product_mode');
    }
}
