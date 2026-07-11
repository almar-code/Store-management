<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // 1️⃣ تحديد اسم الجدول بداخل قاعدة البيانات
    protected $table = 'carts';

    // 2️⃣ تعيين المفتاح الأساسي المخصص لجدول السلة
    protected $primaryKey = 'cart_id';

    // 3️⃣ الحقول المسموح بحفظها جماعياً
    protected $fillable = [
        'customer_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    // ==================== 🔗 العلاقات (Relationships) ====================

    /**
     * علاقة السلة بالمنتج (كل سجل سلة يخص منتج واحد)
     */
    public function product()
    {
        // الحقل الممرر هو p_id وهو المفتاح الأساسي في جدول products
        return $this->belongsTo(Product::class, 'product_id', 'p_id');
    }

    /**
     * علاقة السلة باللون المختار
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'color_id');
    }

    /**
     * علاقة السلة بالمقاس المختار
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'size_id');
    }
}