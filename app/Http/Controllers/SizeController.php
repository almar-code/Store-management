<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use App\Models\Product;

class SizeController extends Controller
{
    public function AddSize($productID){
        try {
            $productName = Product::findOrFail($productID)->p_name;
            return view('Sizes.addsize', compact('productName','productID'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتجات المتعلقه بالمقاس');

        }
        
    }
    public function SizeManagement(){
         try {
            $sizes = Size::with('Product')->get();
            return view('Sizes.sizeManagement', compact('sizes'));
         } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المقاسات');
         }
    }
    public function store(Request $request,$productID)
        {
            $request->validate([
                'size_name' => 'required'
            ]);

            try {

                Size::create([
                    'size_name' => $request->size_name,
                    'p_id' => $productID
                ]);

                return redirect()->back()->with('success', 'تم إضافة مقاس المنتج بنجاح');

            } catch (\Throwable $th) {

                return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة مقاس للمنتج');

            }
        }
    public function edit($id)
        {
            try {
                // جلب المقاس مع بيانات المنتج المرتبط
                $editsize = Size::with('product')->findOrFail($id);

                // الآن $editsize->product يحتوي على المنتج المرتبط
                // يمكننا تمرير كل شيء إلى الصفحة
                return view('Sizes.addsize', compact('editsize'));

            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'المقاس غير موجود');
            }
        }

    public function update(Request $request, $id)
    {
         $request->validate([
                'size_name' => 'required|max:255',
                'product_id' => 'required|exists:products,p_id'
            ]);

            try {
                $size =Size::findOrFail($id);
                $data=[
                   'size_name' =>$request->size_name,
                   'product_id' => $request->p_id
                ];
                
                $size->update($data);

                return redirect('/sizeManagement')->with('success', 'تم التعديل بنجاح');

            } catch (\Throwable $th) {

                return redirect()->back()->with('error', 'حدث خطأ أثناء تعديل  مقاس المنتج');

            }
    }

     public function destroy($id)
    {

        try {

            $size = Size::findOrFail($id);
            $size->delete();

            return redirect()->back()->with('success', 'تم حذف المقاس  بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }

    
}
