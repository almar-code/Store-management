<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\TranslationService;
use App\Jobs\ProcessImageWithGoogleVision;

class ColorsController extends Controller
{
    // مجلد صور المنتجات داخل قرص public
    public const IMAGE_DIR = 'uploads/products';

    public function AddColor($productID)
    {
        $productName = Product::findOrFail($productID)->p_name;
        $colors = ProductController::COLORS;

        return view('Colors.addColor', compact('productName', 'productID', 'colors'));
    }

    public function store(Request $request, $productID)
    {
        $request->validate([
            'productImages' => 'required|array|min:1',
            'productImages.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $savedImages = [];

        DB::beginTransaction();

        try {
            $result = $this->saveColor(
                $request->file('productImages', []),
                $productID,
                $request->colorName,
                $request->productColor
            );

            $savedImages = $result['images'];

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($savedImages as $imageName) {
                self::deleteImageFile($imageName);
            }

            Log::error('Add color failed', [
                'product_id' => $productID,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة اللون');
        }

        // تحليل أول صورة بالذكاء الاصطناعي بعد نجاح الحفظ
        if (!empty($savedImages)) {
            $this->dispatchVisualAnalysis($result['color']->color_id, $savedImages[0]);
        }

        return redirect()->back()->with('success', 'تم إضافة اللون بنجاح');
    }

    /**
     * حفظ لون جديد مع صوره (تُستخدم من هنا ومن ProductController).
     * لا تبدأ معاملة بنفسها، المستدعي هو المسؤول عن DB::beginTransaction.
     *
     * @return array{color: Color, images: string[]}
     */
    public function saveColor(array $images, $productID, ?string $colorName, ?string $colorCode): array
    {
        $colorName = trim((string) $colorName);

        $color = Color::create([
            'color_name' => $colorName,
            'color_name_en' => $this->translateColor($colorName),
            'color_code' => $colorCode ? strtoupper($colorCode) : '#000000',
            'p_id' => $productID,
        ]);

        $storedImages = [];

        try {
            foreach ($images as $image) {
                $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();

                $image->storeAs(self::IMAGE_DIR, $imageName, 'public');
                $storedImages[] = $imageName;

                ProductImage::create([
                    'img_url' => $imageName,
                    'color_id' => $color->color_id,
                ]);
            }
        } catch (\Throwable $e) {
            // حذف ما رُفع قبل الفشل ثم تمرير الخطأ للمستدعي
            foreach ($storedImages as $imageName) {
                self::deleteImageFile($imageName);
            }

            throw $e;
        }

        return [
            'color' => $color,
            'images' => $storedImages,
        ];
    }

    /**
     * إرسال أول صورة لـ Gemini لاستخراج الخصائص البصرية (البحث بالصورة).
     * فشلها لا يؤثر على حفظ اللون.
     */
    public function dispatchVisualAnalysis($colorId, string $imageName): void
    {
        $imagePath = storage_path('app/public/' . self::IMAGE_DIR . '/' . $imageName);

        if (!file_exists($imagePath)) {
            return;
        }

        try {
            ProcessImageWithGoogleVision::dispatch($colorId, $imagePath);
        } catch (\Throwable $e) {
            Log::warning('Visual analysis dispatch failed', [
                'color_id' => $colorId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function deleteImageFile(?string $imageName): void
    {
        if (empty($imageName)) {
            return;
        }

        $path = self::IMAGE_DIR . '/' . $imageName;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
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
            $colors = ProductController::COLORS;

            return view('Colors.addColor', compact('editColor', 'productName', 'colors'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب ألوان ');

        }
    }

    public function update(Request $request, $colorID)
    {
        try {
            $color = Color::findOrFail($colorID);

            // ترجمة اسم اللون (كانت تترجم productName بالخطأ)
            $colorName = trim((string) $request->colorName);

            $color->color_name = $colorName;
            $color->color_name_en = $this->translateColor($colorName);
            $color->color_code = $request->productColor;

            // إذا رفع المستخدم صور جديدة
            if ($request->hasFile('productImages')) {

                // تحقق من checkbox الاحتفاظ بالصور القديمة
                if (!$request->has('keep_old_images')) {
                    // حذف جميع الصور القديمة (مع مسار المجلد الصحيح)
                    foreach ($color->images as $img) {
                        self::deleteImageFile($img->img_url);
                        $img->delete();
                    }
                }

                // رفع الصور الجديدة
                               // رفع الصور الجديدة
                foreach ($request->file('productImages') as $image) {
                    $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();
                    $image->storeAs(self::IMAGE_DIR, $imageName, 'public');

                    // أول صورة جديدة لإعادة التحليل
                    $firstNewImage ??= $imageName;
                    ProductImage::create([
                        'img_url' => $imageName,
                        'color_id' => $color->color_id
                    ]);
                }
            }
            $color->save();
             // إعادة التحليل إذا تغيرت الصورة الأولى للون
            if (!empty($firstNewImage) && !$request->has('keep_old_images')) {
                $this->dispatchVisualAnalysis($color->color_id, $firstNewImage);
            }

            return redirect('/colors/' . $color->p_id)->with('success', 'تم التعديل بنجاح');

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

    private function translateColor(string $colorName): string
    {
        if ($colorName === '') {
            return '';
        }

        try {
            return TranslationService::translate($colorName) ?: $colorName;
        } catch (\Throwable $e) {
            Log::warning('Color translation failed', ['error' => $e->getMessage()]);

            return $colorName;
        }
    }
}