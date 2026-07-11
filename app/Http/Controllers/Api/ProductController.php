<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;
use App\Models\Discount;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class ProductController extends Controller
{
   public function getProducts(Request $request)
{
    // 1. استقبال المعرفات من الـ Query Parameters
    $subcatId = $request->query('subCatId');
    $categoryId = $request->query('category_id');
    $productId = $request->query('product_id'); 
    $customerId = $request->query('customer_id');
    $date = $request->query('date');
    $seed = $request->query('seed', time());
    
    // اعتماد قيم افتراضية منطقية (false)
    $isDiscount = filter_var($request->query('is_discount'), FILTER_VALIDATE_BOOLEAN) ?? false; 
    $isFavorite = filter_var($request->query('is_favorite'), FILTER_VALIDATE_BOOLEAN) ?? false; 

    // 2. بدء بناء الاستعلام الأساسي النظيف
    // $query = Product::with(['colors', 'discount']);
    $query = Product::with([
    'sizes:size_id,size_name,p_id', // جلب المقاسات (حدد الأعمدة التي تريدها للحفاظ على الأداء)
    'colors.images',               // 🌟 جلب الصور المتداخلة التابعة لكل لون بالتحديد (Nested Relation)
    'discount',
]);
$query->addSelect([
    'video_id' => \App\Models\Video::select('video_id')
        ->whereColumn('product_id', 'products.p_id') // ربط جدول الفيديوهات بجدول المنتجات
        ->latest('video_id') // ترتيب لجلب أحدث فيديو
        ->limit(1) // جلب سجل واحد فقط
]);
if ($customerId) {
        $query->withExists(['favoritedBy as is_favorite' => function ($subQuery) use ($customerId) {
            $subQuery->where('favorites.customer_id', $customerId);
        }]);
    }
    // 🌟 أولاً: فلترة الخصومات
    if ($isDiscount) {
        $query->whereHas('discount', function ($subQuery) {
            $subQuery->where('end_date', '>=', now());
        });
    }
    if ($date) {
        
        $query->whereDate('created_at', $date); 
    }

    // 🌟 ثانياً: الفلترة الآمنة والذكية للمفضلات باستخدام whereHas
    if ($isFavorite) {
        // إذا طلب فلاتر المفضلات ولم يرسل آيدي العميل، نوقف الاستعلام لمنع الأخطاء
        if (!$customerId) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id is required when is_favorite is true'
            ], 400);
        }

        // جلب المنتجات التي يملك العميل سجل لها في جدول المفضلة
        $query->whereHas('favoritedBy', function ($subQuery) use ($customerId) {
            $subQuery->where('favorites.customer_id', $customerId);
        });
    }

    // 3. تطبيق الفلترة بناءً على الأقسام
    if ($subcatId) {
        $query->where('subcat_id', $subcatId);
    } elseif ($categoryId) {
        $query->whereIn('subcat_id', function ($subQuery) use ($categoryId) {
            $subQuery->select('subcat_id') 
                     ->from('subcategories')
                     ->where('cat_id', $categoryId);
        });
    }

    // 4. تثبيت المنتج المستهدف في الصدارة
    if ($productId) {
        $query->orderByRaw("p_id = ? DESC", [$productId]); 
    }

    // 5. الترتيب العشوائي لبقية المنتجات بناءً على الـ Seed
    $query->inRandomOrder($seed);

    // 6. جلب المنتجات على دفعات (تم زيادة الرقم لـ 15 لأداء أفضل)
    $products = $query->paginate(10); 

    // 7. إرجاع الاستجابة
    return response()->json([
        'status' => true,
        'message' => 'Products fetched successfully',
        'data' => $products->items(), 
        'meta' => [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'has_more' => $products->hasMorePages(),
            'seed' => (int)$seed,
        ]
    ], 200);
}

}
