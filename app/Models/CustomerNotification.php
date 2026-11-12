<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerNotification extends Model
{
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'customer_id',
        'type',
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
