<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
   protected $primaryKey = 'customer_id';

    protected $fillable = [
        'supabase_id',
        'name',
        'email',
        'phone'
    ];

public function favorites()
{
    // البارامتر الأول: الموديل المرتبط (Product)
    // البارامتر الثاني: اسم الجدول الوسيط الذي أنشأناه (favorites)
    // البارامتر الثالث: اسم المفتاح الخارجي للعميل في جدول المفضلة
    // البارامتر الرابع: اسم المفتاح الخارجي للمنتج في جدول المفضلة
    return $this->belongsToMany(Product::class, 'favorites', 'customer_id', 'p_id')
                ->withTimestamps(); // لكي يقوم بتحديث حقول timestamps تلقائياً عند الإضافة
}
}
