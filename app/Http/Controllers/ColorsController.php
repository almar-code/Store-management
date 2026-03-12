<?php

namespace App\Http\Controllers;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Product;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
class ColorsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function AddColor($productID)
    {
        $productName = Product::findOrFail($productID)->p_name;

        return view('Colors.addColor', compact('productName', 'productID'));
    }
    public function store(Request $request, $productID)
    {
        try {
            // التحقق من الحقول
            $request->validate([
                'productImages.*' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);
            $tr = new GoogleTranslate('en');

            // ترجمة النصوص
            $colorNameEn = '';
            $colorName = '';
            if ($request->filled('colorName')) { // يتحقق إذا الحقل ليس فارغ
                $colorNameEn = $tr->translate($request->colorName);
                $colorName = $request->colorName;
            }                // حفظ اللون
            $color = Color::create([

                'color_name' => $colorName,
                'color_name_en' => $colorNameEn,

                'color_code' => $request->productColor,

                'p_id' => $productID

            ]);
            // رفع الصور
            if ($request->hasFile('productImages')) {

                $images = $request->file('productImages');

                foreach ($images as $index => $image) {

                    // إنشاء اسم فريد للصورة
                    $imageName = time() . '_' . rand(1, 10000) . '.' . $image->extension();

                    // تخزين الصورة داخل storage/app/public/uploads/products
                    $image->storeAs('uploads/products', $imageName, 'public');
                    // حفظ اسم الصورة فقط في جدول الصور
                    ProductImage::create([
                        'img_url' => $imageName,
                        'color_id' => $color->color_id
                    ]);

                }

            }


            return redirect()->back()->with('success', 'تم إضافة اللون بنجاح');

        } catch (\Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة اللون');

        }
    }
    public function Colors($productID)
    {
        try {

            $productColor = Color::with('images')
                ->where('p_id', $productID)
                ->get();

            return view('Colors.colors', compact('productColor'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب ألوان المنتج');

        }
    }
    public function edit($colorID)
    {

        try {

            $editColor = Color::findOrFail($colorID);

            $productName = Product::where('p_id', $editColor->p_id)->value('p_name');

            return view('Colors.addColor', compact('editColor', 'productName'));

        } catch (\Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب ألوان ');

        }
    }
    public function update(Request $request, $colorID)
    {
        try {
            $color = Color::findOrFail($colorID);
            $tr = new GoogleTranslate('en');

            // ترجمة الاسم
            $colorNameEn = '';
            $colorName = '';
            if ($request->filled('colorName')) {
                $colorNameEn = $tr->translate($request->colorName);
                $colorName = $request->colorName;
            }

            $color->color_name = $colorName;
            $color->color_name_en = $colorNameEn;
            $color->color_code = $request->productColor;

            // إذا رفع المستخدم صور جديدة
            if ($request->hasFile('productImages')) {

                // تحقق من checkbox الاحتفاظ بالصور القديمة
            if (!$request->has('keep_old_images')) {
                // حذف جميع الصور القديمة
                foreach ($color->images as $img) {
                    if (Storage::disk('public')->exists($img->img_url)) {
                        Storage::disk('public')->delete($img->img_url);
                    }
                    $img->delete(); // حذف السجل من قاعدة البيانات
                }
            }

                // رفع الصور الجديدة
                foreach ($request->file('productImages') as $image) {
                    $imageName = time() . '_' . rand(1, 10000) . '.' . $image->extension();
                    $image->storeAs('uploads/products', $imageName, 'public');

                    ProductImage::create([
                        'img_url' => $imageName,
                        'color_id' => $color->color_id
                    ]);
                }
            }
            $color->save();

            return redirect('/colors/' . $color->p_id)->with('success', 'تم التعديل بنجاح');//  الرجوع إلى صفحة الألوان الخاصة بالمنتج

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تعديل ألوان ');
        }
    }

    // حذف اللون
    public function destroy($colorID)
    {
        try {

            $color = Color::findOrFail($colorID);

            $color->delete();

            return redirect()->back()->with('success', 'تم حذف اللون بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }
}
