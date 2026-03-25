<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisement'; // مهم لأن اسم الجدول مفرد

    protected $primaryKey = 'ads_id'; // لأن المفتاح الأساسي ليس id

    protected $fillable = [
        'AdsName',
        'AdsImage',
        'AdsLink',
        'is_active',
        'expires_at'
    ];
}
