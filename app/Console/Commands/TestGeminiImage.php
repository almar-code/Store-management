<?php

namespace App\Console\Commands;

use App\Services\GeminiImageService;
use Illuminate\Console\Command;
use Throwable;

class TestGeminiImage extends Command
{
    protected $signature = 'gemini:test-image {path}';

    protected $description =
        'Safely test Gemini image embedding and description';

    public function handle(GeminiImageService $gemini): int
    {
        $path = $this->argument('path');

        if (!is_file($path)) {
            $this->error('لم يتم العثور على ملف الصورة.');
            return self::FAILURE;
        }

        $this->info('جارٍ اختبار المتجه البصري...');

        try {
            $vector = $gemini->embedImage($path);

            $this->info('نجح إنشاء المتجه.');
            $this->line('عدد الأبعاد: ' . count($vector));

            // لا نطبع المتجه كاملًا.
            $this->line(
                'أول 3 قيم: ' .
                json_encode(array_slice($vector, 0, 3))
            );

            $this->info('جارٍ استخراج وصف العباية...');

            $description = $gemini->describeImage($path);

            $this->info('نجح تحليل الصورة.');
            $this->line(
                json_encode(
                    $description,
                    JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_UNICODE
                )
            );

            $this->info(
                'نجح الاختبار. لم يتم تعديل قاعدة البيانات.'
            );

            return self::SUCCESS;
       } catch (Throwable $e) {
    $this->error(
        'فشل الاختبار: ' . $e->getMessage()
    );

    $this->line(
        'لم يتم حفظ أي بيانات في قاعدة البيانات.'
    );

    return self::FAILURE;
}
    }
}
