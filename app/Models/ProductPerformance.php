<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPerformance extends Model
{
    // اسم الجدول والمفتاح الأساسي
    protected $table = 'product_performance';
    protected $primaryKey = 'performance_id';

    protected $fillable = [
        'p_id',
        'analysis_date',
        'status',
        'score',
        'cluster',
        'diagnosis',
        'views',
        'cart_adds',
        'favorites',
        'shares',
        'sales',
        'indicators',
    ];

    // تحويل المؤشرات من JSON إلى مصفوفة تلقائياً
    protected $casts = [
        'indicators'    => 'array',
        'score'         => 'float',
        'analysis_date' => 'date',
    ];

    // المنتج
    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
}