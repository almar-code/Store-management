<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStat extends Model
{
    // 1. اسم الجدول والمفتاح الأساسي
    protected $table = 'product_stats';
    protected $primaryKey = 'stat_id';

    // 2. الحقول المسموح تعبئتها (Mass Assignment)
    protected $fillable = [
        'p_id',
        'views_count',
        'cart_adds_count',
        'favorites_count',
        'shares_count',
        'sales_count',
        'positive_comments_count',
        'negative_comments_count',
    ];

    // 3. علاقة عكسية مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
}