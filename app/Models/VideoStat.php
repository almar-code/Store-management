<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoStat extends Model
{
    use HasFactory;

    // 1. تحديد اسم الجدول في قاعدة البيانات بدقة
    protected $table = 'video_stats';

    // 2. السماح بالحقول ليتمكن الـ Controller من إنشائها وتعديلها (Mass Assignment)
    protected $fillable = [
        'video_id',
        'likes_count',
        'saves_count',
        'shares_count'
    ];

    // 3. علاقة عكسية مع جدول الفيديوهات (اختياري ولكن مفيد)
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id', 'video_id');
    }
}