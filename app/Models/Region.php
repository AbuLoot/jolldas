<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Kalnoy\Nestedset\NodeTrait;

class Region extends Model
{
    use NodeTrait;

    protected $table = 'regions';

    public $timestamps = false;

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    // public function tracks()
    // {
    //     return $this->belongsToMany(Track::class);
    // }
}
