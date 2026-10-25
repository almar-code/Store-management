<?php

namespace App\Jobs;

use App\Services\GeminiImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessImageWithGoogleVision implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    protected int $colorId;
    protected string $imagePath;

    public function __construct($colorId, $imagePath)
    {
        $this->colorId = (int) $colorId;
        $this->imagePath = (string) $imagePath;
    }

    public function handle(GeminiImageService $gemini): void
    {
        if (!is_file($this->imagePath)) {
            Log::error('Gemini visual feature job: image file not found', [
                'color_id' => $this->colorId,
                'path' => $this->imagePath,
            ]);

            return;
        }

        try {
            $hash = $this->calculatePerceptualHash($this->imagePath);

            if ($hash === null) {
                throw new \RuntimeException(
                    'تعذر حساب البصمة الإدراكية للصورة. تحقق من تفعيل PHP GD.'
                );
            }

            // استخراج التضمين البصري والوصف الخاص بالعباية.
            $embedding = $gemini->embedImage($this->imagePath);
            $description = $gemini->describeImage($this->imagePath);

            if (!is_array($description)) {
                throw new \RuntimeException(
                    'استجابة وصف الصورة من Gemini ليست مصفوفة JSON صالحة.'
                );
            }

            $vector = $embedding['embedding']
                ?? $embedding['values']
                ?? (array_is_list($embedding) ? $embedding : null);

            if (!is_array($vector) || count($vector) < 64) {
                throw new \RuntimeException(
                    'استجابة Gemini لا تحتوي على متجه بصري صالح.'
                );
            }

            // لا نخمن اللون من كلمات عامة مثل blue أو light blue.
            // يُحفظ فقط HEX صالح أرجعه Gemini.
            $colorHex = $description['main_color_hex'] ?? null;

            if (
                !is_string($colorHex)
                || !preg_match('/^#[0-9a-fA-F]{6}$/', trim($colorHex))
            ) {
                $colorHex = null;
            } else {
                $colorHex = strtoupper(trim($colorHex));
            }

            // حفظ الخصائص المهمة للملابس فقط، وليس الكلمات العامة غير المفيدة.
            $labels = [];

            $allowedKeys = [
                'item_type',
                'main_color',
                'pattern',
                'embroidery',
                'sleeves',
                'cut',
                'fabric_texture',
                'visible_details',
            ];

            foreach ($allowedKeys as $key) {
                $value = $description[$key] ?? null;

                if (is_string($value) && trim($value) !== '') {
                    $labels[] = trim($value);
                } elseif (is_array($value)) {
                    foreach ($value as $item) {
                        if (is_string($item) && trim($item) !== '') {
                            $labels[] = trim($item);
                        }
                    }
                }
            }

            $labels = array_values(array_unique($labels));

            $featuresJson = [
                'provider' => 'gemini',
                'embedding_model' => config(
                    'services.gemini.embedding_model',
                    'gemini-embedding-2'
                ),
                'embedding_dimensions' => count($vector),
                'embedding' => array_values(
                    array_map('floatval', $vector)
                ),
                'description_model' => config(
                    'services.gemini.vision_model',
                    'gemini-2.5-flash'
                ),
                'garment_description' => $description,
                'schema_version' => 3,
            ];

            DB::table('product_visual_features')->updateOrInsert(
                ['color_id' => $this->colorId],
                [
                    'image_hash' => $hash,
                    'dominant_color' => $colorHex,
                    'image_labels' => implode(', ', $labels),
                    'image_features_json' => json_encode(
                        $featuresJson,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                        | JSON_THROW_ON_ERROR
                    ),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            Log::info('Gemini visual features saved', [
                'color_id' => $this->colorId,
                'hash' => $hash,
                'embedding_dimensions' => count($vector),
                'main_color' => $colorHex,
                'description_color' => $description['main_color'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('Gemini visual feature job failed', [
                'color_id' => $this->colorId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * حساب DCT perceptual hash بحجم 64 بت تقريبًا.
     * يجب استخدام الخوارزمية نفسها في الفهرسة والبحث.
     */
    private function calculatePerceptualHash(string $path): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($path);

        if (!$info || empty($info['mime'])) {
            return null;
        }

        $image = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp')
                ? @imagecreatefromwebp($path)
                : false,
            default => false,
        };

        if (!$image) {
            return null;
        }

        $size = 32;
        $thumb = imagecreatetruecolor($size, $size);

        imagecopyresampled(
            $thumb,
            $image,
            0,
            0,
            0,
            0,
            $size,
            $size,
            imagesx($image),
            imagesy($image)
        );

        $gray = [];

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $rgb = imagecolorat($thumb, $x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $gray[$y][$x] =
                    0.299 * $r + 0.587 * $g + 0.114 * $b;
            }
        }

        imagedestroy($image);
        imagedestroy($thumb);

        $coeff = [];

        for ($v = 0; $v < 8; $v++) {
            for ($u = 0; $u < 8; $u++) {
                $sum = 0.0;

                for ($y = 0; $y < $size; $y++) {
                    for ($x = 0; $x < $size; $x++) {
                        $sum += $gray[$y][$x]
                            * cos(((2 * $x + 1) * $u * M_PI) / (2 * $size))
                            * cos(((2 * $y + 1) * $v * M_PI) / (2 * $size));
                    }
                }

                $cu = $u === 0
                    ? sqrt(1 / $size)
                    : sqrt(2 / $size);

                $cv = $v === 0
                    ? sqrt(1 / $size)
                    : sqrt(2 / $size);

                $coeff[] = $sum * $cu * $cv;
            }
        }

        // استبعاد معامل DC.
        $low = array_slice($coeff, 1);
        $sorted = $low;
        sort($sorted, SORT_NUMERIC);

        $median = $sorted[(int) floor(count($sorted) / 2)];

        $bits = '0';

        foreach ($low as $value) {
            $bits .= $value >= $median ? '1' : '0';
        }

        $hex = '';

        foreach (str_split($bits, 4) as $chunk) {
            $hex .= dechex(bindec($chunk));
        }

        return strtolower(str_pad($hex, 16, '0', STR_PAD_LEFT));
    }
}
