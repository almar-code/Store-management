<?php

namespace App\Console\Commands;

use App\Http\Controllers\ColorsController;
use App\Jobs\ProcessImageWithGoogleVision;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IndexMissingVisualFeatures extends Command
{
    protected $signature = 'visual:index-missing
                            {--limit=50 : أقصى عدد ألوان في كل تشغيل}
                            {--gap=20 : ثواني بين كل مهمة والتي بعدها}';

    protected $description = 'تحليل الألوان التي ليس لها خصائص بصرية في product_visual_features';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $gap = max(0, (int) $this->option('gap'));

        // الألوان التي ليس لها سجل في جدول الميزات
        $colorIds = DB::table('colors')
            ->leftJoin('product_visual_features as f', 'f.color_id', '=', 'colors.color_id')
            ->whereNull('f.color_id')
            ->orderBy('colors.color_id')
            ->limit($limit)
            ->pluck('colors.color_id');

        if ($colorIds->isEmpty()) {
            $this->info('كل الألوان محللة');

            return self::SUCCESS;
        }

        // أول صورة لكل لون
        $firstImages = DB::table('product_images')
            ->whereIn('color_id', $colorIds)
            ->orderBy('img_id')
            ->get(['color_id', 'img_url'])
            ->unique('color_id')
            ->pluck('img_url', 'color_id');

        $queued = 0;
        $skipped = 0;

        foreach ($colorIds as $colorId) {
            $imageName = $firstImages[$colorId] ?? null;
            $path = $imageName
                ? storage_path('app/public/' . ColorsController::IMAGE_DIR . '/' . $imageName)
                : null;

            if (!$path || !is_file($path)) {
                $this->warn("لون {$colorId}: لا توجد صورة");
                $skipped++;

                continue;
            }

            // توزيع المهام زمنياً حتى لا نضغط على Gemini
            ProcessImageWithGoogleVision::dispatch($colorId, $path)
                ->delay(now()->addSeconds($queued * $gap));

            $queued++;
        }

        $this->info("أُرسل للتحليل: {$queued} | بدون صورة: {$skipped}");

        return self::SUCCESS;
    }
}