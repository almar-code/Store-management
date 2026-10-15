<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    use HasFactory;

    protected $table = 'product_comments';
    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'customer_id',
        'p_id',
        'comment_ar',
        'comment_en',
        'stars',
    ];

   public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
}
    

    // ربط العلاقة مع جدول المنتجات
    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
}