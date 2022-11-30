<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_status', 'track_id', 'status_id');
    }
}
