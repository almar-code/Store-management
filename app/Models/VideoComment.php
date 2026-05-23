<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoComment extends Model
{
    // 1. تحديد اسم الجدول في قاعدة البيانات (تأكد من مطابقتة لاسم جدولك)
    protected $table = 'video_comments';

    // 2. الحقول المسموح بإدخال البيانات إليها مباشرة
    protected $fillable = [
        'video_id',
        'customer_id',
        'comment_text',
    ];

    /**
     * ربط التعليق بالعميل الذي قام بكتابته.
     * 
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        // تم تحديد المفتاح الخارجي والمفتاح الأساسي بدقة كما أرفقت في كودك
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}