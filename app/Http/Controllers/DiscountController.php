<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Product;
use Carbon\Carbon;


class DiscountController extends Controller
{
    
   public function AddDiscount($productID){
        try {
            $productPrice = Product::findOrFail($productID)->p_price;
            return view('discount.addDiscount', compact('productPrice','productID'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتج المتعلق بالخصم');
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
                $discount =  $request->discount_perce;
                // لا يتجاوز 40% من السعر القديم
                $maxDiscount = $price * 0.4;
                if ($discount > $maxDiscount) {
                    return redirect()->back()->with('error', 'لا يمكن أن تتجاوز قيمة الخصم 40٪ من السعر الأصلي');
                }


                $duration = (int) $request->duration; // <- هذا هو التصحيح
                $startDate = Carbon::today();
                $endDate = $startDate->copy()->addDays($duration);
                Discount::create([
                    'p_id' => $id,
                    'discount_perce' => $request->discount_perce,
                    'end_date' => $endDate
                ]);
                return redirect('/products')->with('success', 'تم إضافة الخصم بنجاح');
            }catch (\Throwable $th) {
                return redirect()->back()->with('error', 'حدث خطاء اثناء إضافة الخصم ');
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
