<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * 2. دالة التبديل (Toggle): إضافة للمفضلة أو حذفها تلقائياً بضغطة زر واحدة
     */
    public function toggleFavorite(Request $request)
    {
        // التحقق من صحة البيانات القادمة من فلاتر
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'product_id' => 'required|exists:products,p_id',
        ]);

        // جلب العميل
        $customer = Customer::findOrFail($request->customer_id);

        // استخدام الدالة السحرية toggle لإضافة أو حذف المنتج تلقائياً
        $result = $customer->favorites()->toggle($request->product_id);

        // فحص النتيجة ومعرفة هل تم الإضافة (attached) أم الحذف (detached)
        if (count($result['attached']) > 0) {
            return response()->json([
                'status' => true,
                'is_favorite' => true,
                'message' => 'تم إضافة المنتج للمفضلة بنجاح'
            ], 200);
        }

        return response()->json([
            'status' => true,
            'is_favorite' => false,
            'message' => 'تم إزالة المنتج من المفضلة بنجاح'
        ], 200);
    }
    /**
 * جلب العدد الإجمالي للمنتجات المفضلة لعميل معين
 */
public function getFavoriteCount(Request $request)
{
    // التحقق من إرسال آيدي العميل
    $request->validate([
        'customer_id' => 'required|exists:customers,customer_id',
    ]);

    // جلب العميل وحساب عدد السجلات في جدول المفضلة المشترك
    $customer = Customer::findOrFail($request->customer_id);
    $count = $customer->favorites()->count();

    return response()->json([
        'status' => true,
        'favorite_count' => $count
    ], 200);
}
}