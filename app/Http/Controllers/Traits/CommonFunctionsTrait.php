<?php

namespace App\Http\Controllers\Traits;

use Intervention\Image\Facades\Image;

trait CommonFunctionsTrait
{
    /**
     * رفع صور متعددة أو واحدة مع ضغطها بشكل افتراضي
     * @param array|\Illuminate\Http\UploadedFile $images
     * @param string $folder المسار داخل storage/app/public
     * @return array أسماء الصور المرفوعة
     */
    public function uploadImages($images, $folder = 'uploads')
    {
        $uploadedNames = [];

        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $image) {
            $imageName = time() . '_' . rand(1, 10000) . '.' . $image->extension();

            // ضغط الصورة بشكل افتراضي
            $img = Image::make($image)
                ->resize(1024, null, function ($constraint) {
                    $constraint->aspectRatio(); // الحفاظ على النسبة
                    $constraint->upsize();      // عدم تكبير الصور الصغيرة
                });

            // حفظ الصورة مع ضغط متوسط (75%)
            $img->save(storage_path("app/public/{$folder}/{$imageName}"), 75);

            $uploadedNames[] = $imageName;
        }

        return $uploadedNames;
    }

    /**
     * دالة وهمية للتوضيح مثل الترجمة أو أي وظيفة مشتركة مستقبلية
     */
    public function translateText($text, $lang = 'en')
    {
        return "[{$lang}] " . $text;
    }
}