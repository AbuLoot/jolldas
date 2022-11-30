<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    public function parent()
    {
    	return $this->morphTo();
    }

    public function getCreatedAtAttribute($value)
    {
        return date('j '.trans('date.month.'.date('F', strtotime($value))).', Y', strtotime($value));
    }
}