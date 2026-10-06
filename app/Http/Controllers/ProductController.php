<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\Discount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\TranslationService;

class ProductController extends Controller
{
    // عرض المنتجات
    public function Products()
    {
        try {

            $products = Product::with([
                'discount' => function ($query) {
                    $query->where('end_date', '>=', Carbon::today());
                }
            ])->get();

            return view('Products.products', compact('products'));

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء جلب المنتجات');
        }
    }


    // صفحة إضافة منتج
    public function AddProduct()
    {
        try {

            $subCategories = Subcategory::all();

            return view(
                'Products.addproduct',
                compact('subCategories')
            );

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء فتح الصفحة');
        }
    }


    // حفظ المنتج
    public function store(Request $request)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric',
            'productImages.*' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {

            $exists = Product::where(
                'p_name',
                $request->productName
            )->exists();

            if ($exists && !$request->boolean('allow_duplicate')) {

                return redirect()->back()
                    ->withInput()
                    ->with('duplicate_product', true);
            }


            // الترجمة
            $productNameEn = TranslationService::translate(
                $request->productName
            );

            $productDescriptionEn = TranslationService::translate(
                $request->productDescription ?? ''
            );

            $colorNameEn = '';

            if ($request->filled('colorName')) {

                $colorNameEn = TranslationService::translate(
                    $request->colorName
                );
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
            $p_imageName = null;

            if ($request->hasFile('productImages')) {

                foreach ($request->file('productImages') as $index => $image) {

                    $imageName =
                        time() . '_' .
                        rand(1, 10000) . '.' .
                        $image->getClientOriginalExtension();

                    $destinationPath =
                        public_path('storage/uploads/products');

                    $image->move(
                        $destinationPath,
                        $imageName
                    );

                    ProductImage::create([
                        'img_url' => $imageName,
                        'color_id' => $color->color_id
                    ]);

                    if ($index == 0) {
                        $p_imageName = $imageName;
                    }
                }
            }


            // تعيين الصورة الرئيسية
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


            // إرسال بيانات المنتج إلى n8n
            try {

                $webhookUrl = env('N8N_WEBHOOK_URL');

                if ($webhookUrl) {

                    $response = Http::post($webhookUrl, [
                        'p_name' => $product->p_name,
                        'phons' => [
                            "967733357396",
                            "967779271679",
                            "967733357396"
                        ],
                        'p_price' => $product->p_price,
                        'p_description' => $product->p_description,
                    ]);

                    if (!$response->successful()) {

                        Log::warning(
                            'n8n Webhook returned status: ' .
                            $response->status()
                        );
                    }

                } else {

                    Log::warning(
                        'N8N_WEBHOOK_URL is not defined.'
                    );
                }

            } catch (\Exception $e) {

                Log::error(
                    'Failed to send data to n8n: ' .
                    $e->getMessage()
                );
            }


            return redirect()->back()
                ->with('success', 'تم إضافة المنتج بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المنتج');
        }
    }


    // تعديل المنتج
    public function edit($id)
    {
        try {

            $editProduct = Product::with('subcategory')
                ->findOrFail($id);

            $subCategories = Subcategory::all();

            return view(
                'Products.addproduct',
                compact('editProduct', 'subCategories')
            );

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'المنتج غير موجود');
        }
    }


    // تحديث المنتج
    public function update(Request $request, $id)
    {
        $request->validate([
            'productName' => 'required|max:255',
            'productPrice' => 'required|numeric',
            'productSubcategory' =>
                'required|exists:subcategories,subcat_id',
        ]);

        try {

            $product = Product::findOrFail($id);


            // التحقق من اسم المنتج في منتج آخر
            $exists = Product::where(
                'p_name',
                $request->productName
            )
            ->where(
                'p_id',
                '!=',
                $id
            )
            ->exists();


            if ($exists && !$request->boolean('allow_duplicate')) {

                return redirect()->back()
                    ->withInput()
                    ->with('duplicate_product', true);
            }


            // الترجمة
            $name_en = TranslationService::translate(
                $request->productName
            );

            $productDescriptionEn = TranslationService::translate(
                $request->productDescription ?? ''
            );


            // تحديث بيانات المنتج
            $product->p_name = $request->productName;
            $product->p_name_en = $name_en;
            $product->p_price = $request->productPrice;
            $product->p_description = $request->productDescription;
            $product->p_description_en = $productDescriptionEn;
            $product->subcat_id = $request->productSubcategory;


            // رفع صورة جديدة
            if ($request->hasFile('productImages')) {

                $images = $request->file('productImages');

                $image = is_array($images)
                    ? $images[0]
                    : $images;


                // حذف الصورة القديمة
                if (!empty($product->p_image)) {

                    $oldImagePath = public_path(
                        'storage/uploads/products/' .
                        $product->p_image
                    );

                    if (\File::exists($oldImagePath)) {
                        \File::delete($oldImagePath);
                    }
                }


                // حفظ الصورة الجديدة
                $imageName =
                    time() . '_' .
                    $image->getClientOriginalName();

                $image->move(
                    public_path('storage/uploads/products'),
                    $imageName
                );

                $product->p_image = $imageName;
            }


            $product->save();


            return redirect('/products')
                ->with('success', 'تم التعديل بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التعديل');
        }
    }


    // حذف المنتج
    public function destroy($id)
    {
        try {

            $product = Product::findOrFail($id);

            $imagePath = public_path(
                'storage/uploads/products/' .
                $product->p_image
            );

            if (
                !empty($product->p_image) &&
                \File::exists($imagePath)
            ) {

                \File::delete($imagePath);
            }


            $product->delete();


            return redirect()->back()
                ->with('success', 'تم حذف المنتج وصورته بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}
