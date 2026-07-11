<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * 📥 1. جلب محتويات السلة لعميل معين بكافة التفاصيل
     * GET /api/cart?customer_id=5
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        // جلب سجلات السلة مع العلاقات المتداخلة المتطابقة مع كود فلاتر
        $cartItems = DB::table('carts')
            ->where('customer_id', $request->customer_id)
            ->get();

        // سنقوم بتركيب مصفوفة البيانات يدوياً أو عبر علاقات الـ Eloquent لضمان تطابق الهيكل تماماً
        $formattedCart = $cartItems->map(function ($item) {
            // جلب المنتج الأساسي
            $product = DB::table('products')->where('p_id', $item->product_id)->first();
            
            // جلب الخصم النشط للمنتج
            $discount = DB::table('discounts')->where('p_id', $item->product_id)->first();

            // جلب المقاس المحدد
            $size = $item->size_id ? DB::table('sizes')->where('size_id', $item->size_id)->first() : null;

            // جلب اللون المحدد
            $color = $item->color_id ? DB::table('colors')->where('color_id', $item->color_id)->first() : null;

            // جلب صور هذا اللون المحدد من جدول product_images
            $colorImages = $item->color_id 
                ? DB::table('product_images')->where('color_id', $item->color_id)->get(['img_id', 'img_url', 'color_id'])
                : [];

            return [
                'cart_id' => $item->cart_id, // افترضنا أن المفتاح الأساسي لجدول السلة هو cart_id
                'product_id' => $item->product_id,
                'p_name' => $product ? $product->p_name : '',
                'p_name_en' => $product ? $product->p_name_en : null,
                'p_price' => $product ? $product->p_price : 0,
                'quantity' => $item->quantity,
                'size' => $size ? [
                    'size_id' => $size->size_id,
                    'size_name' => $size->size_name,
                    'p_id' => $size->p_id,
                ] : null,
                'color' => $color ? [
                    'color_id' => $color->color_id,
                    'color_name' => $color->color_name,
                    'color_name_en' => $color->color_name_en ?? null,
                    'color_code' => $color->color_code,
                    'images' => $colorImages
                ] : null,
                'discount' => $discount ? [
                    'discount_id' => $discount->discount_id,
                    'discount_perce' => $discount->discount_perce,
                    'end_date' => $discount->updated_at, // أو الحقل المتوفر لديك للمدة
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'تم جلب محتويات السلة بنجاح',
            'data' => $formattedCart
        ], 200);
    }

    /**
     * ➕ 2. إضافة منتج جديد إلى السلة أو زيادة كميته إن كان موجوداً مسبقاً
     * POST /api/cart/add
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'product_id'  => 'required|integer',
            'quantity'    => 'required|integer|min:1',
            'color_id'    => 'nullable|integer',
            'size_id'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        // التحقق مما إذا كان نفس العميل قد أضاف نفس المنتج بنفس اللون والمقاس مسبقاً
        $existingItem = DB::table('carts')
            ->where('customer_id', $request->customer_id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            // إذا كان موجوداً، نزيد الكمية فقط
            DB::table('carts')
                ->where('cart_id', $existingItem->cart_id)
                ->increment('quantity', $request->quantity);

            return response()->json([
                'status' => true,
                'message' => 'المنتج موجود مسبقاً، تم زيادة الكمية ',
            ], 200);
        }

        // إذا كان منتجاً جديداً بخصائصه، نقوم بإضافته
        DB::table('carts')->insert([
            'customer_id' => $request->customer_id,
            'product_id'  => $request->product_id,
            'color_id'    => $request->color_id,
            'size_id'     => $request->size_id,
            'quantity'    => $request->quantity,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة المنتج إلى السلة بنجاح'
        ], 201);
    }

    /**
     * 🔄 3. تحديث كمية صنف معين بداخل السلة (زيادة / نقصان مباشر)
     * PUT /api/cart/update/{id}
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        $updated = DB::table('carts')
            ->where('cart_id', $id)
            ->update([
                'quantity' => $request->quantity,
                'updated_at' => now()
            ]);

        if ($updated) {
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث كمية السلة بنجاح'
            ], 200);
        }

        return response()->json(['status' => false, 'message' => 'سجل السلة غير موجود'], 444);
    }

    /**
     * ❌ 4. حذف صنف تماماً من السلة
     * DELETE /api/cart/delete/{id}
     */
    public function destroy(string $id)
    {
        $deleted = DB::table('carts')->where('cart_id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح'
            ], 200);
        }

        return response()->json(['status' => false, 'message' => 'فشل الحذف، الصنف غير موجود'], 444);
    }

    /**
     * دالة عرض عنصر واحد (غير مستخدمة حالياً بناءً على الراوتات التفصيلية الخاصة بك)
     */
    public function show(string $id)
    {
        return response()->json(['status' => false, 'message' => 'Method Not Allowed'], 405);
    }
}