<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;
use App\Models\Discount;
use Carbon\Carbon;
class ProductController extends Controller
{

    // عرض المنتجات
    public function Products()
    {
        try {

             $products = Product::with(['discount' => function ($query) {
            $query->where('end_date', '>=', Carbon::today());}])->get();
            return view('Products.products', compact('products'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتجات');

        }
    }


    // صفحة إضافة منتج
    public function AddProduct()
    {
        $subCategories = Subcategory::all();
        try {

            return view('Products.addproduct', compact('subCategories'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء فتح الصفحة');

        }
    }


    // حفظ المنتج
    public function store(Request $request)
    {
        try {
            $p_imageName = null;

            // التحقق من الحقول
            $request->validate([
                'productName' => 'required|string|max:255',
                'productDescription' => 'nullable|string',
                'productPrice' => 'required|numeric',
                'productImages.*' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);



            //  الترجمة

            $tr = new GoogleTranslate('en');
             $tr->setOptions([
            'verify' => false
        ]);
            // ترجمة النصوص
            $productNameEn = $tr->translate($request->productName);
            $productDescriptionEn = $tr->translate($request->productDescription);
            $colorNameEn = '';
            if ($request->filled('colorName')) { // يتحقق إذا الحقل ليس فارغ
                $colorNameEn = $tr->translate($request->colorName);
            }


            // حفظ المنتج
            $product = Product::create([

                'p_name' => $request->productName,
                'p_name_en' => $productNameEn,

                'p_description' => $request->productDescription,
                'p_description_en' => $productDescriptionEn,

                'p_price' => $request->productPrice,
                'p_image' => null,
                'subcat_id' => $request->productSubcategory

            ]);



            // حفظ اللون
            $color = Color::create([

                'color_name' => $request->colorName,
                'color_name_en' => $colorNameEn,

                'color_code' => $request->productColor,

                'p_id' => $product->p_id

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

                    // نجعل أول صورة صورة المنتج الرئيسية
                    if ($index == 0) {
                        $p_imageName = $imageName;
                    }
                }

            }
            if ($p_imageName) {

                $product->update([

                    'p_image' => $p_imageName

                ]);

            }

            // حفظ المقاس
            if ($request->filled('productSize')) {
                Size::create([
                    'size_name' => $request->productSize,
                    'p_id' => $product->p_id
                ]);
            }




            return redirect()->back()->with('success', 'تم إضافة المنتج بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة المنتج')->withInput();
            
        }

    }


    // عند الضغط على تعديل
    public function edit($id)
    {
        try {

            // جلب المنتج المطلوب تعديله
            $editProduct = Product::with('subcategory')->findOrFail($id);
            $subCategories = Subcategory::all();

            // فتح صفحة الفورم مع البيانات
            return view('Products.addproduct', compact('editProduct', 'subCategories'));
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'المنتج غير موجود');

        }
    }


    // تحديث البيانات
    public function update(Request $request, $id)
    {
        $request->validate([
            'productName' => 'required|max:255',
            'productPrice' => 'required|numeric',
            'productDescription' => 'required',
            'productSubcategory' => 'required|exists:subcategories,subcat_id',
        ]);

        try {
            $product = Product::findOrFail($id);

            // ترجمة للإنجليزية
            $tr = new GoogleTranslate('en');
             $tr->setOptions([
            'verify' => false
        ]);
            $name_en = $tr->translate($request->productName);
            $productDescriptionEn = $tr->translate($request->productDescription);


            // تحديث بيانات المنتج
            $product->p_name = $request->productName;
            $product->p_name_en = $name_en;
            $product->p_price = $request->productPrice;
            $product->p_description = $request->productDescription;
            $product->p_description_en = $request->productDescriptionEn;
            $product->subcat_id = $request->productSubcategory;

            // معالجة رفع الصور (إذا اختار المستخدم صور جديدة)
            if ($request->hasFile('productImages')) {
                $image = $request->file('productImages')[0]; // أول صورة فقط حالياً

                // حذف الصورة القديمة إذا كانت موجودة
                if ($product->p_image && Storage::disk('public')->exists('uploads/products/' . $product->p_image)) {
                    Storage::disk('public')->delete('uploads/products/' . $product->p_image);
                }

                // إنشاء اسم فريد للصورة
                $imageName = time() . '_' . $image->getClientOriginalName();

                // رفع الصورة باستخدام storeAs داخل storage/app/public/uploads/products
                $image->storeAs('uploads/products', $imageName, 'public');

                // حفظ اسم الصورة الجديدة في قاعدة البيانات
                $product->p_image = $imageName;
            }

            $product->save();

            return redirect('/products')->with('success', 'تم التعديل بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');
        }
    }


    // حذف المنتج
    public function destroy($id)
    {
        try {

            $products = Product::findOrFail($id);

            $products->delete();

            return redirect()->back()->with('success', 'تم حذف المنتج');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }

}
