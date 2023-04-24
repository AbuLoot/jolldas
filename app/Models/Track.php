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
        return $this->belongsToMany(Status::class, 'track_status')->withPivot('region_id', 'created_at', 'updated_at');
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'track_status', 'track_id', 'region_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
