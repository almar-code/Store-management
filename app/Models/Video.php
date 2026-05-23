<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos'; // اسم الجدول
    protected $primaryKey = 'video_id'; // المفتاح الأساسي

    protected $fillable = [
        'video_path',
        'product_id',
    ];

    // علاقة الفيديو مع المنتج (اختياري)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'p_id');
    }
    // داخل كلاس Video في ملف App\Models\Video.php
public function stats()
{
    return $this->hasOne(VideoStat::class, 'video_id', 'video_id');
}
public function comments()
{
    return $this->hasMany(
        VideoComment::class,
        'video_id',
        'video_id'
    );
}
}