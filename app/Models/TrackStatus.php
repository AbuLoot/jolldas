<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackStatus extends Model
{
    use HasFactory;

    protected $table = 'track_status';

    protected $fillable = [
        'track_id',
        'status_id',
        'region_id',
    ];
}
