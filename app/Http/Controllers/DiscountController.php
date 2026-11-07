<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Product;
use Carbon\Carbon;


class DiscountController extends Controller
{

    public function AddDiscount($productID)
    {
        try {
            $productPrice = Product::findOrFail($productID)->p_price;
            return view('discount.addDiscount', compact('productPrice', 'productID'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتج المتعلق بالخصم');
        }
    }

    public function index()
    {
        try {
            // جلب المنتجات التي تحتوي على خصم ساري المفعول فقط
            $products = Product::whereHas('discount', function ($query) {
                $query->where('end_date', '>=', Carbon::today());
            })
                ->with([
                    'subcategory',
                    'discount' => function ($query) {
                        // تحميل بيانات الخصم الفعال مع المنتج
                        $query->where('end_date', '>=', Carbon::today());
                    }
                ])
                ->get();

            // وضع الخصومات: تظهر أزرار تعديل الخصم وحذفه فقط
            $discountMode = true;

            return view('Products.products', compact('products', 'discountMode'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتجات المخفضة');
        }
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'discount_perce' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:1',
        ]);
        try {

            $price = (float) $request->price;
            $discount = $request->discount_perce;
            // لا يتجاوز 40% من السعر القديم
            $maxDiscount = $price * 0.4;
            if ($discount > $maxDiscount) {
                return redirect()->back()->with('error', 'لا يمكن أن تتجاوز قيمة الخصم 40٪ من السعر الأصلي');
            }


            $duration = (int) $request->duration;
            $startDate = Carbon::today();
            $endDate = $startDate->copy()->addDays($duration);
            Discount::create([
                'p_id' => $id,
                'discount_perce' => $request->discount_perce,
                'end_date' => $endDate
            ]);
            return redirect('/products')->with('success', 'تم إضافة الخصم بنجاح');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء إضافة الخصم ');
        }
    }

    // صفحة تعديل الخصم (نفس صفحة الإضافة مع البيانات)
    public function edit($id)
    {
        try {
            $editDiscount = Discount::findOrFail($id);
            $productID = $editDiscount->p_id;
            $productPrice = Product::findOrFail($productID)->p_price;

            // المدة المتبقية بالأيام لتظهر في الحقل
            $remainingDays = max(1, (int) Carbon::today()->diffInDays(
                Carbon::parse($editDiscount->end_date)->startOfDay(),
                false
            ));

            return view('discount.addDiscount', compact('productPrice', 'productID', 'editDiscount', 'remainingDays'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الخصم');
        }
    }

    // حفظ تعديل الخصم
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'discount_perce' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:1',
        ]);
        try {
            $discount = Discount::findOrFail($id);

            $price = (float) $request->price;
            // لا يتجاوز 40% من السعر القديم
            if ($request->discount_perce > $price * 0.4) {
                return redirect()->back()->with('error', 'لا يمكن أن تتجاوز قيمة الخصم 40٪ من السعر الأصلي');
            }

            $discount->update([
                'discount_perce' => $request->discount_perce,
                'end_date' => Carbon::today()->addDays((int) $request->duration),
            ]);

            return redirect('/discounts')->with('success', 'تم تعديل الخصم بنجاح');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تعديل الخصم');
        }
    }

    public function destroy($id)
    {
        try {

            $discount = Discount::findOrFail($id);

            $discount->delete();

            return redirect()->back()->with('success', 'تم حذف الخصم');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }
}