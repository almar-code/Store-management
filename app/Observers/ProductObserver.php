<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ProductStatService;

class ProductObserver
{
    /**
     * يُنفَّذ تلقائياً بعد إضافة أي منتج جديد عبر Eloquent
     * فيُنشئ له صف إحصائيات بقيم صفرية
     */
    public function created(Product $product): void
    {
        ProductStatService::ensure($product->p_id);
    }
}