<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * 1. جلب كافة الإعلانات النشطة وغير المنتهية (لتغذية السلايدر في التطبيق)
     */
   public function getAds()
{
    // جلب كافة الإعلانات مباشرة للتأكد من ظهورها في السلايدر
    $ads = Advertisement::all(); 

    return response()->json([
        'status' => true,
        'data' => $ads
    ]);
}
    /**
     * 2. جلب المنتجات المرتبطة بإعلان عام محدد عبر الجدول الوسيط
     */
    public function getGeneralAdProducts($adsId)
    {
        try {
            // البحث عن الإعلان مع جلب المنتجات المرتبطة به علاقة Many-to-Many أو عبر الجدول الوسيط
            $ad = Advertisement::with('products')->findOrFail($adsId);

            return response()->json([
                'status' => true,
                'ad_name' => $ad->AdsName,
                'data' => $ad->products // المنتجات المرتبطة بالإعلان العام
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'الإعلان غير موجود أو حدث خطأ',
                'error' => $th->getMessage()
            ], 404);
        }
    }
}