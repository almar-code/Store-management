<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductPerformanceController extends Controller
{
    public function index()
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. جلب المنتجات التي عمرها من 1 إلى 30 يوم
            |--------------------------------------------------------------------------
            */

            $products = DB::table('products')
                ->join(
                    'subcategories',
                    'products.subcat_id',
                    '=',
                    'subcategories.subcat_id'
                )
                ->join(
                    'categories',
                    'subcategories.cat_id',
                    '=',
                    'categories.cat_id'
                )
                ->whereBetween(
                    DB::raw('DATEDIFF(CURDATE(), products.created_at)'),
                    [1, 30]
                )
                ->select(
                    'products.p_id',
                    'products.p_name',
                    'products.p_name_en',
                    'products.p_price',
                    'products.p_image',
                    'products.created_at',

                    'subcategories.subcat_name',
                    'subcategories.subcat_name_en',

                    'categories.cat_name',
                    'categories.cat_name_en'
                )
                ->get();


            /*
            |--------------------------------------------------------------------------
            | 2. تجهيز بيانات المنتجات للنموذج
            |--------------------------------------------------------------------------
            */

            $data = $products->map(function ($product) {

                /*
                |--------------------------------------------------------------------------
                | عمر المنتج
                |--------------------------------------------------------------------------
                */

                $productAge = Carbon::parse($product->created_at)
                    ->diffInDays(Carbon::now());


                /*
                |--------------------------------------------------------------------------
                | الخصم
                |--------------------------------------------------------------------------
                |
                | نأخذ أحدث خصم مرتبط بالمنتج.
                | إذا لم يوجد خصم → 0
                |
                */

                $discount = DB::table('discounts')
                    ->where('p_id', $product->p_id)
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now()->toDateString());
                    })
                    ->orderByDesc('created_at')
                    ->value('discount_perce');

                $discount = $discount ?? 0;


                /*
                |--------------------------------------------------------------------------
                | عدد مرات الإضافة للمفضلة
                |--------------------------------------------------------------------------
                */

                $favorites = DB::table('favorites')
                    ->where('p_id', $product->p_id)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | عدد المنتجات المضافة إلى السلة
                |--------------------------------------------------------------------------
                |
                | نستخدم quantity لأن المنتج يمكن إضافته بأكثر من قطعة.
                |
                */

                $cartAdds = DB::table('carts')
                    ->where('product_id', $product->p_id)
                    ->sum('quantity');


                /*
                |--------------------------------------------------------------------------
                | عدد المشاركات
                |--------------------------------------------------------------------------
                |
                | shares موجودة في video_stats وليس products.
                |
                */

                $shares = DB::table('video_stats')
                    ->join(
                        'videos',
                        'video_stats.video_id',
                        '=',
                        'videos.video_id'
                    )
                    ->where('videos.product_id', $product->p_id)
                    ->sum('video_stats.shares_count');


                /*
                |--------------------------------------------------------------------------
                | التعليقات الإيجابية
                |--------------------------------------------------------------------------
                |
                | النجوم 4 أو 5 = إيجابي
                |
                */

                $positiveComments = DB::table('product_comments')
                    ->where('p_id', $product->p_id)
                    ->where('stars', '>=', 4)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | التعليقات السلبية
                |--------------------------------------------------------------------------
                |
                | النجوم 1 إلى 3 = سلبي
                |
                */

                $negativeComments = DB::table('product_comments')
                    ->where('p_id', $product->p_id)
                    ->where('stars', '<=', 3)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | المقاس
                |--------------------------------------------------------------------------
                |
                | لا يوجد في قاعدة البيانات preferred_size.
                | لذلك نأخذ أول مقاس مرتبط بالمنتج مؤقتاً.
                |
                */

                $preferredSize = DB::table('sizes')
                    ->where('p_id', $product->p_id)
                    ->orderBy('size_id')
                    ->value('size_name');

                $preferredSize = $preferredSize ?? '52';


                /*
                |--------------------------------------------------------------------------
                | اللون
                |--------------------------------------------------------------------------
                |
                | لا يوجد preferred_color في قاعدة البيانات.
                | لذلك نأخذ أول لون مرتبط بالمنتج مؤقتاً.
                |
                */

                $preferredColor = DB::table('colors')
                    ->where('p_id', $product->p_id)
                    ->orderBy('color_id')
                    ->value('color_name_en');

                $preferredColor = $preferredColor ?? 'Black';


                /*
                |--------------------------------------------------------------------------
                | المشاهدات
                |--------------------------------------------------------------------------
                |
                | لا يوجد جدول أو عمود لتخزين Views حالياً.
                | لذلك نضع قيمة وهمية مؤقتة.
                |
                | سيتم استبدالها لاحقاً عندما نضيف نظام تسجيل المشاهدات.
                |
                */

                $views = 10000;


                /*
                |--------------------------------------------------------------------------
                | اسم الفئة
                |--------------------------------------------------------------------------
                |
                | النموذج تم تدريبه على أسماء الفئات الإنجليزية.
                |
                */

                $category = $product->cat_name_en
                    ?? $product->cat_name
                    ?? 'Simple Abayas';


                /*
                |--------------------------------------------------------------------------
                | تجهيز بيانات المنتج
                |--------------------------------------------------------------------------
                */

                return [

                    'product_id' => $product->p_id,

                    'price' => (float) $product->p_price,

                    'discount' => (int) $discount,

                    'product_age' => $productAge,

                    'category' => $category,

                    'views' => $views,

                    'favorites' => $favorites,

                    'cart_adds' => $cartAdds,

                    'shares' => $shares,

                    'positive_comments' => $positiveComments,

                    'negative_comments' => $negativeComments,

                    'preferred_size' => $preferredSize,

                    'preferred_color' => $preferredColor,
                ];
            })->values()->toArray();


            /*
            |--------------------------------------------------------------------------
            | 3. إرسال المنتجات إلى نموذج Machine Learning
            |--------------------------------------------------------------------------
            */

            $response = Http::timeout(60)
    ->acceptJson()
    ->asJson()
    ->post(
        'https://smartabayastore.pythonanywhere.com/predict',
        $data
    );


            /*
            |--------------------------------------------------------------------------
            | 4. التحقق من استجابة النموذج
            |--------------------------------------------------------------------------
            */
            \Log::info('ML DATA', $data);

            
            if (!$response->successful()) {
                return back()->with(
                    'error',
                    'Flask Error: HTTP ' . $response->status() 
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 5. الحصول على نتائج التصنيف
            |--------------------------------------------------------------------------
            */

            $predictions = $response->json();


            /*
            |--------------------------------------------------------------------------
            | 6. ربط نتيجة النموذج بالمنتج
            |--------------------------------------------------------------------------
            */

            $results = collect($predictions)->map(function ($prediction) {

                $product = DB::table('products')
                    ->where('p_id', $prediction['product_id'])
                    ->first();

                return [

                    'product' => $product,

                    'product_id' => $prediction['product_id'],

                    'performance' => $prediction['performance'],

                ];

            });


            /*
            |--------------------------------------------------------------------------
            | 7. إرسال النتائج إلى صفحة Blade
            |--------------------------------------------------------------------------
            */

            return view(
                'Products.productperformance',
                compact('results')
            );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | في حالة حدوث أي خطأ
            |--------------------------------------------------------------------------
            */

            return back()->with(
                'error',
                'حدث خطأ: ' . $e->getMessage()
            );
        }
    }
}