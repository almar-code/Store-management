<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductStatService;
class CartController extends Controller
{
    /**
     * 🔑 1. جلب أو إنشاء رقم السلة الحقيقي (cart_id) الخاص بالعميل
     * POST /api/cart/get-or-create
     */
    public function getOrCreateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        // جلب سلة العميل أو إنشاؤها فوراً إن لم تكن موجودة
        $cart = Cart::firstOrCreate(
            ['customer_id' => $request->customer_id]
        );

        return response()->json([
            'status' => true,
            'message' => 'تم جلب رقم السلة الحقيقي بنجاح',
            'cart_id' => $cart->cart_id,
        ], 200);
    }

    /**
     * 📥 2. جلب محتويات السلة لعميل معين بكافة التفاصيل
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

        // جلب السلة الخاصة بالعميل
        $cart = Cart::where('customer_id', $request->customer_id)->first();

        if (!$cart) {
            return response()->json([
                'status' => true,
                'message' => 'تم جلب محتويات السلة بنجاح',
                'cart_id' => null,
                'data' => []
            ], 200);
        }

        // جلب كافة عناصر السلة
        $cartItems = CartItem::where('cart_id', $cart->cart_id)->get();

        $formattedCart = $cartItems->map(function ($item) {
            $product = DB::table('products')->where('p_id', $item->product_id)->first();
            $discount = DB::table('discounts')->where('p_id', $item->product_id)->first();
            $size = $item->size_id ? DB::table('sizes')->where('size_id', $item->size_id)->first() : null;
            $color = $item->color_id ? DB::table('colors')->where('color_id', $item->color_id)->first() : null;

            $colorImages = $item->color_id 
                ? DB::table('product_images')->where('color_id', $item->color_id)->get(['img_id', 'img_url', 'color_id'])
                : [];

                       // ألوان ومقاسات المنتج المتاحة لتعديلها من السلة
            $productColors = DB::table('colors')->where('p_id', $item->product_id)->get()->map(function ($c) {
                return [
                    'color_id' => $c->color_id,
                    'color_name' => $c->color_name,
                    'color_name_en' => $c->color_name_en ?? null,
                    'color_code' => $c->color_code,
                    'images' => DB::table('product_images')->where('color_id', $c->color_id)->get(['img_id', 'img_url', 'color_id']),
                ];
            });
            $productSizes = DB::table('sizes')->where('p_id', $item->product_id)->get(['size_id', 'size_name', 'p_id']);

            return [
                'cart_id' => $item->cart_item_id, // يُرجع cart_item_id للتعديل والحذف في فلاتر
                'product_id' => $item->product_id,
                'p_name' => $product ? $product->p_name : '',
                'p_name_en' => $product ? $product->p_name_en : null,
                'p_price' => $product ? $product->p_price : 0,
                'p_image' => $product ? $product->p_image : null,
                'subcat_id' => $product ? $product->subcat_id : 0,
                'p_description' => $product ? $product->p_description : null,
                'p_description_en' => $product ? ($product->p_description_en ?? null) : null,
                'product_colors' => $productColors,
                'product_sizes' => $productSizes,
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
                    'end_date' => $discount->updated_at,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'تم جلب محتويات السلة بنجاح',
            'cart_id' => $cart->cart_id, // رقم السلة الرئيسي
            'data' => $formattedCart
        ], 200);
    }

    /**
     * ➕ 3. إضافة منتج جديد إلى السلة أو زيادة كميته إن كان موجوداً مسبقاً
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

        // جلب سلة العميل أو إنشاؤها فوراً إذا لم تكن موجودة
        $cart = Cart::firstOrCreate([
            'customer_id' => $request->customer_id
        ]);
        // زيادة عداد الإضافة للسلة (كل ضغطة "أضف للسلة" تُحسب مرة واحدة)
        ProductStatService::increment((int) $request->product_id, 'cart_adds_count');

        // التحقق مما إذا كان نفس المنتج بنفس اللون والمقاس موجوداً مسبقاً داخل السلة
        $existingItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $request->quantity);
            $cart->touch();

            return response()->json([
                'status' => true,
                'message' => 'المنتج موجود مسبقاً، تم زيادة الكمية',
                'cart_id' => $cart->cart_id
            ], 200);
        }

        // إضافة المنتج كعنصر جديد
        CartItem::create([
            'cart_id'    => $cart->cart_id,
            'product_id' => $request->product_id,
            'color_id'   => $request->color_id,
            'size_id'    => $request->size_id,
            'quantity'   => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة المنتج إلى السلة بنجاح',
            'cart_id' => $cart->cart_id
        ], 201);
    }

        /**
     * 4. تحديث صنف داخل السلة (الكمية او اللون او المقاس)
     * PUT /api/cart/update/{id}
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|integer|min:1',
            'color_id' => 'sometimes|nullable|integer',
            'size_id'  => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        $item = CartItem::where('cart_item_id', $id)->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'سجل السلة غير موجود'], 444);
        }

        $data = $request->only(['quantity', 'color_id', 'size_id']);

        // التأكد ان اللون والمقاس يتبعان نفس المنتج
        if (!empty($data['color_id']) && !DB::table('colors')->where('color_id', $data['color_id'])->where('p_id', $item->product_id)->exists()) {
            return response()->json(['status' => false, 'message' => 'اللون غير متاح لهذا المنتج'], 420);
        }
        if (!empty($data['size_id']) && !DB::table('sizes')->where('size_id', $data['size_id'])->where('p_id', $item->product_id)->exists()) {
            return response()->json(['status' => false, 'message' => 'المقاس غير متاح لهذا المنتج'], 420);
        }

        $colorId = array_key_exists('color_id', $data) ? $data['color_id'] : $item->color_id;
        $sizeId = array_key_exists('size_id', $data) ? $data['size_id'] : $item->size_id;

        // دمج الصنف اذا وجد نفس المنتج بنفس اللون والمقاس
        $duplicate = CartItem::where('cart_id', $item->cart_id)
            ->where('cart_item_id', '!=', $item->cart_item_id)
            ->where('product_id', $item->product_id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();

        if ($duplicate) {
            $duplicate->increment('quantity', $data['quantity'] ?? $item->quantity);
            $item->delete();

            return response()->json([
                'status' => true,
                'merged' => true,
                'message' => 'تم دمج المنتج مع صنف مطابق في السلة'
            ], 200);
        }

        $item->update($data);
        Cart::where('cart_id', $item->cart_id)->first()?->touch();

        return response()->json([
            'status' => true,
            'merged' => false,
            'message' => 'تم تحديث السلة بنجاح'
        ], 200);
    }
    /**
     * ❌ 5. حذف صنف من السلة
     * DELETE /api/cart/delete/{id}
     */
    public function destroy(string $id)
    {
        $item = CartItem::where('cart_item_id', $id)->first();

        if ($item) {
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح'
            ], 200);
        }

        return response()->json(['status' => false, 'message' => 'فشل الحذف، الصنف غير موجود'], 444);
    }
        /**
     * 6. حذف كل اصناف السلة لعميل معين
     * DELETE /api/cart/clear?customer_id=5
     */
    public function clear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 420);
        }

        $cart = Cart::where('customer_id', $request->customer_id)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->cart_id)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم حذف كل المنتجات من السلة'
        ], 200);
    }
}