<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $table = 'tracks';

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'track_status');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
