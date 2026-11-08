<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductStatService;
use Illuminate\Http\Request;

class ProductStatController extends Controller
{
    /**
     * 👁️ تسجيل مشاهدة منتج (عند فتح صفحة تفاصيل المنتج في التطبيق)
     * POST /api/products/{p_id}/view
     */
    public function recordView(Request $request, $p_id)
    {
        $request->validate([
            'customer_id' => 'nullable|integer',
        ]);

        if (!\App\Models\Product::where('p_id', $p_id)->exists()) {
            return response()->json(['status' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        $counted = ProductStatService::recordView(
            (int) $p_id,
            $request->customer_id ? (int) $request->customer_id : null,
            $request->ip()
        );

        return response()->json([
            'status'  => true,
            'counted' => $counted,
        ], 200);
    }

    /**
     * 🔗 تسجيل مشاركة منتج (عند الضغط على زر المشاركة)
     * POST /api/products/{p_id}/share
     */
    public function recordShare($p_id)
    {
        if (!\App\Models\Product::where('p_id', $p_id)->exists()) {
            return response()->json(['status' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        ProductStatService::increment((int) $p_id, 'shares_count');

        return response()->json(['status' => true], 200);
    }
}