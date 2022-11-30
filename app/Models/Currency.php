<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    public $timestamps = false;

    public function companies()
    {
        return $this->hasMany('App\Models\Company');
    }
}
