<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public function getDateAttribute()
    {
    	return strtr(date("j F Y ", strtotime($this->created_at)), trans('data.month'));
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'parent');
    }
}
