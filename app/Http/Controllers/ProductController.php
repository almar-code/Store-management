<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\TranslationService;
use App\Services\GeminiImageService;
use App\Jobs\SendNewProductEmails;

class ProductController extends Controller
{
    // المقاسات الجاهزة المعروضة كمربعات (ويمكن إضافة مقاس مخصص من الواجهة)
    public const SIZES = ['50', '52', '54', '56', '58', '60', '62'];

    // اقتراحات الألوان (الاسم => كود اللون)
    public const COLORS = [
        'أسود' => '#000000',
        'أبيض' => '#FFFFFF',
        'أوف وايت' => '#FAF9F6',
        'كريمي' => '#FFFDD0',
        'بيج' => '#F5F5DC',
        'رملي' => '#C2B280',
        'جملي' => '#C19A6B',
        'نود' => '#E3BC9A',
        'بني' => '#8B4513',
        'بني فاتح' => '#A0785A',
        'بني غامق' => '#5C4033',
        'شوكولاتي' => '#7B3F00',
        'قهوة' => '#6F4E37',
        'رمادي' => '#808080',
        'رمادي فاتح' => '#D3D3D3',
        'رمادي غامق' => '#505050',
        'فضي' => '#C0C0C0',
        'كحلي' => '#000080',
        'أزرق' => '#0000FF',
        'أزرق فاتح' => '#ADD8E6',
        'سماوي' => '#87CEEB',
        'تركواز' => '#40E0D0',
        'تيفاني' => '#0ABAB5',
        'أخضر' => '#008000',
        'زيتي' => '#556B2F',
        'زمردي' => '#50C878',
        'نعناعي' => '#98FF98',
        'أحمر' => '#FF0000',
        'عنابي' => '#800000',
        'نبيتي' => '#722F37',
        'خمري' => '#5E1914',
        'وردي' => '#FFC0CB',
        'وردي فاتح' => '#FFD1DC',
        'فوشي' => '#FF00FF',
        'بنفسجي' => '#8A2BE2',
        'أرجواني' => '#800080',
        'ليلكي' => '#C8A2C8',
        'موف' => '#E0B0FF',
        'أصفر' => '#FFFF00',
        'خردلي' => '#E1AD01',
        'ذهبي' => '#FFD700',
        'برتقالي' => '#FFA500',
        'مشمشي' => '#FBCEB1',
        'خوخي' => '#FFE5B4',
        'تراكوتا' => '#E2725B',
    ];

    // عرض المنتجات
    public function Products()
    {
        try {
            $products = Product::with([
                'subcategory',
                'discount' => function ($query) {
                    $query->where('end_date', '>=', Carbon::today());
                }
            ])->get();

            return view('Products.products', compact('products'));

        } catch (\Throwable $e) {
            Log::error('Products list failed', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء جلب المنتجات');
        }
    }

    // صفحة إضافة منتج
    public function AddProduct()
    {
        try {
            $subCategories = Subcategory::all();
            $sizes = self::SIZES;
            $colors = self::COLORS;

            return view(
                'Products.addproduct',
                compact('subCategories', 'sizes', 'colors')
            );

        } catch (\Throwable $e) {
            Log::error('Add product page failed', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء فتح الصفحة');
        }
    }

    // التحقق من تكرار اسم المنتج (AJAX) مع بيانات المنتج الموجود
    public function checkName(Request $request)
    {
        $product = Product::where('p_name', trim((string) $request->query('name', '')))
            ->when($request->query('except'), fn ($query, $id) => $query->where('p_id', '!=', $id))
            ->first();

        if (!$product) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            'product' => [
                'name' => $product->p_name,
                'price' => $product->p_price,
                'image' => $product->p_image
                    ? asset('storage/' . ColorsController::IMAGE_DIR . '/' . $product->p_image)
                    : null,
            ],
        ]);
    }

    // تحليل صورة العباية بالذكاء الاصطناعي (AJAX)
    public function analyzeImage(Request $request, GeminiImageService $gemini)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ], [
            'image.required' => 'الرجاء اختيار صورة',
            'image.image' => 'الملف المختار ليس صورة',
            'image.mimes' => 'الصيغ المسموحة: jpg, jpeg, png',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت',
        ]);

        try {
            $result = $gemini->describeAbayaForProduct(
                $request->file('image')->getRealPath(),
                array_keys(self::COLORS)
            );

            if (($result['is_abaya'] ?? true) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'الصورة لا تبدو لعباية، يمكنك إدخال البيانات يدوياً',
                ], 422);
            }

            $description = trim((string) ($result['description'] ?? ''));

            if ($description === '') {
                throw new \RuntimeException('Empty description from Gemini');
            }

            // اسم اللون يُقبل فقط إذا كان من القائمة
            $colorName = $result['color_name'] ?? null;
            $colorName = is_string($colorName) && array_key_exists(trim($colorName), self::COLORS)
                ? trim($colorName)
                : null;

            // كود اللون الفعلي، وإن لم يصلح نستخدم كود اسم اللون
            $colorHex = $result['color_hex'] ?? null;
            $colorHex = is_string($colorHex) && preg_match('/^#[0-9A-Fa-f]{6}$/', trim($colorHex))
                ? strtoupper(trim($colorHex))
                : ($colorName ? self::COLORS[$colorName] : null);

            return response()->json([
                'success' => true,
                'description' => $description,
                'color_name' => $colorName,
                'color_hex' => $colorHex,
            ]);

        } catch (\Throwable $e) {
            Log::error('Product image AI analysis failed', [
                'error' => $e->getMessage(),
            ]);

            // ضغط مؤقت على خوادم Gemini بعد تجربة كل النماذج
            $busy = preg_match('/HTTP (429|500|503|504)|timed out/i', $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $busy
                    ? 'خدمة الذكاء الاصطناعي مشغولة حالياً، حاول بعد قليل أو أدخل البيانات يدوياً'
                    : (config('app.debug')
                        ? 'تعذر التحليل: ' . $e->getMessage()
                        : 'تعذر تحليل الصورة، يمكنك إدخال البيانات يدوياً'),
            ], $busy ? 503 : 500);
        }
    }

    // حفظ المنتج
    public function store(Request $request)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|gt:0',
            'productDescription' => 'required|string',
            'productSubcategory' => 'required|exists:subcategories,subcat_id',
            'productColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colorName' => 'required|string|max:50',
            'sizes' => 'required|array|min:1',
            'sizes.*' => ['distinct', 'string', 'max:10', 'regex:/^[\p{L}\p{N}\-\/ ]+$/u'],
            'productImages' => 'required|array|min:1',
            'productImages.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ], $this->validationMessages());

        // التحقق من التكرار (احتياطي، الفحص الأساسي يتم قبل الإرسال)
        if (
            $this->nameExists($request->productName) &&
            !$request->boolean('allow_duplicate')
        ) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_product', true);
        }

        // ترجمة المنتج (خارج المعاملة حتى لا يوقف فشلها الحفظ)
        $productNameEn = $this->translate($request->productName);
        $productDescriptionEn = $this->translate($request->productDescription);

        $uploadedImages = [];
        $colorResult = null;

        DB::beginTransaction();

        try {
            // حفظ المنتج
            $product = Product::create([
                'p_name' => $request->productName,
                'p_name_en' => $productNameEn,
                'p_description' => $request->productDescription,
                'p_description_en' => $productDescriptionEn,
                'p_price' => $request->productPrice,
                'p_image' => null,
                'subcat_id' => $request->productSubcategory,
            ]);

            // حفظ اللون وصوره عن طريق كنترولر الألوان
            $colorResult = app(ColorsController::class)->saveColor(
                $request->file('productImages'),
                $product->p_id,
                $request->colorName,
                $request->productColor
            );

            $uploadedImages = $colorResult['images'];

            // تعيين الصورة الرئيسية
            $product->update([
                'p_image' => $uploadedImages[0] ?? null,
            ]);

            // حفظ كل مقاس في سجل مستقل (المقاسات الجاهزة + المخصصة)
            $sizes = array_unique(array_map('trim', $request->input('sizes', [])));

            foreach ($sizes as $size) {
                Size::create([
                    'size_name' => $size,
                    'p_id' => $product->p_id,
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($uploadedImages as $imageName) {
                ColorsController::deleteImageFile($imageName);
            }

            Log::error('Add product failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المنتج');
        }

        // تحليل الصورة للبحث بالصورة (عبر كنترولر الألوان، بعد نجاح الحفظ)
        if (!empty($uploadedImages)) {
            app(ColorsController::class)->dispatchVisualAnalysis(
                $colorResult['color']->color_id,
                $uploadedImages[0]
            );
        }

        // إشعارات المنتج الجديد (فشلها لا يلغي إضافة المنتج)
        try {
            SendNewProductEmails::dispatch($product->p_id);
        } catch (\Throwable $e) {
            Log::warning('New product emails dispatch failed', [
                'product_id' => $product->p_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    // تعديل المنتج
    public function edit($id)
    {
        try {
            $editProduct = Product::with('subcategory')->findOrFail($id);
            $subCategories = Subcategory::all();
            $sizes = self::SIZES;
            $colors = self::COLORS;

            return view(
                'Products.addproduct',
                compact('editProduct', 'subCategories', 'sizes', 'colors')
            );

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'المنتج غير موجود');
        }
    }

    // تحديث المنتج
    public function update(Request $request, $id)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|gt:0',
            'productDescription' => 'required|string',
            'productSubcategory' => 'required|exists:subcategories,subcat_id',
            'productImages' => 'nullable|array',
            'productImages.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ], $this->validationMessages());

        $product = Product::find($id);

        if (!$product) {
            return redirect('/products')
                ->with('error', 'المنتج غير موجود');
        }

        // التحقق من اسم المنتج في منتج آخر
        if (
            $this->nameExists($request->productName, $id) &&
            !$request->boolean('allow_duplicate')
        ) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_product', true);
        }

        $newImage = null;
        $oldImage = null;

        try {
            $product->p_name = $request->productName;
            $product->p_name_en = $this->translate($request->productName);
            $product->p_price = $request->productPrice;
            $product->p_description = $request->productDescription;
            $product->p_description_en = $this->translate($request->productDescription);
            $product->subcat_id = $request->productSubcategory;

            // رفع صورة رئيسية جديدة
            if ($request->hasFile('productImages')) {
                $image = $request->file('productImages')[0];
                $newImage = time() . '_' . Str::random(8) . '.' . $image->extension();
                $image->storeAs(ColorsController::IMAGE_DIR, $newImage, 'public');

                $oldImage = $product->p_image;
                $product->p_image = $newImage;
            }

            $product->save();

        } catch (\Throwable $e) {
            ColorsController::deleteImageFile($newImage);

            Log::error('Update product failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التعديل');
        }

        // حذف الصورة القديمة فقط إذا لم تكن مستخدمة في صور الألوان
        if (
            !empty($oldImage) &&
            !ProductImage::where('img_url', $oldImage)->exists()
        ) {
            ColorsController::deleteImageFile($oldImage);
        }

        return redirect('/products')
            ->with('success', 'تم التعديل بنجاح');
    }

    // حذف المنتج
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()
                ->with('error', 'المنتج غير موجود');
        }

        // جمع كل صور المنتج قبل الحذف
        $colorIds = Color::where('p_id', $product->p_id)->pluck('color_id');

        $images = ProductImage::whereIn('color_id', $colorIds)
            ->pluck('img_url')
            ->push($product->p_image)
            ->filter()
            ->unique();

        try {
            $product->delete();
        } catch (\Throwable $e) {
            Log::error('Delete product failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'تعذر حذف المنتج، قد يكون مرتبطاً بطلبات أو بيانات أخرى');
        }

        // حذف الملفات بعد نجاح حذف المنتج فقط
        foreach ($images as $imageName) {
            ColorsController::deleteImageFile($imageName);
        }

        return redirect()->back()
            ->with('success', 'تم حذف المنتج وصوره بنجاح');
    }

    // ===================== دوال مساعدة =====================

    private function nameExists(string $name, $exceptId = null): bool
    {
        return Product::where('p_name', trim($name))
            ->when($exceptId, fn ($query) => $query->where('p_id', '!=', $exceptId))
            ->exists();
    }

    private function translate(?string $text): string
    {
        if (blank($text)) {
            return '';
        }

        try {
            return TranslationService::translate($text) ?: $text;
        } catch (\Throwable $e) {
            Log::warning('Translation failed', ['error' => $e->getMessage()]);

            return $text;
        }
    }

    private function validationMessages(): array
    {
        return [
            'productName.required' => 'الرجاء إدخال اسم المنتج',
            'productName.max' => 'اسم المنتج طويل جداً',
            'productPrice.required' => 'الرجاء إدخال سعر المنتج',
            'productPrice.numeric' => 'الرجاء إدخال قيمة صحيحة',
            'productPrice.gt' => 'السعر يجب أن يكون أكبر من صفر',
            'productDescription.required' => 'الرجاء إدخال وصف المنتج',
            'productSubcategory.required' => 'الرجاء اختيار فئة المنتج',
            'productSubcategory.exists' => 'الفئة المختارة غير موجودة',
            'productColor.required' => 'الرجاء اختيار لون المنتج',
            'productColor.regex' => 'كود اللون غير صحيح',
            'colorName.required' => 'الرجاء إدخال اسم اللون',
            'sizes.required' => 'الرجاء اختيار مقاس واحد على الأقل',
            'sizes.min' => 'الرجاء اختيار مقاس واحد على الأقل',
            'sizes.*.max' => 'المقاس يجب ألا يتجاوز 10 خانات',
            'sizes.*.regex' => 'المقاس يقبل حروفاً وأرقاماً فقط',
            'sizes.*.distinct' => 'يوجد مقاس مكرر',
            'productImages.required' => 'الرجاء اختيار صورة واحدة على الأقل',
            'productImages.*.image' => 'الرجاء إدخال صور فقط',
            'productImages.*.mimes' => 'الصيغ المسموحة: jpg, jpeg, png',
            'productImages.*.max' => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت',
            'productImages.*.uploaded' => 'فشل رفع الصورة، قد يكون حجمها كبيراً',
        ];
    }
}