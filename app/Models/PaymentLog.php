<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    const TYPE_WAITING = 1;
    const TYPE_SUCCESS = 2;
    const TYPE_CANCEL = 3;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'description'
    ];
}
