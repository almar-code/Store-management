<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVisualFeature;
use App\Services\GeminiImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * البحث بالصورة على ثلاث مراحل:
 * 1. متجه + هاش + لون (نفس اللون فقط).
 * 2. متجه + هاش بدون لون (نفس التصميم بلون آخر).
 * 3. احتياطي: هاش الصورة الرئيسية للمنتجات فقط.
 */
class VisualSearchController extends Controller
{
    private const PER_PAGE = 10;
    private const IMAGE_DIR = 'uploads/products/';

    // حد أدنى للتشابه الدلالي في المرحلتين 1 و2
    private const MIN_EMBEDDING = 0.78;

    // المرحلة 1
    private const STAGE1_MIN_SCORE = 0.68;
    private const STAGE1_MIN_COLOR = 0.85;

    // المرحلة 2
    private const STAGE2_MIN_SCORE = 0.70;

    // المرحلة 3 (هاش فقط)
    private const HASH_STRONG = 0.90;
    private const HASH_MIN = 0.80;

    public function getVisualSearchProducts(
        Request $request,
        GeminiImageService $gemini
    ) {
        set_time_limit(120);

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_url' => 'nullable|string|max:2048',
            'page' => 'nullable|integer|min:1',
            'seed' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->error('بيانات البحث غير صحيحة.', 422, $validator->errors());
        }

        $imagePath = $this->resolveQueryImage($request);

        if (!$imagePath) {
            return $this->error('تعذر الوصول إلى صورة صالحة. أرسل image أو image_url صالحاً.', 422);
        }

        try {
            $query = $this->analyzeQueryImage($imagePath, $gemini);

            [$matches, $mode] = $this->findMatches($query);

            Log::info('Visual search result', [
                'mode' => $mode,
                'query_color' => $query['color'],
                'has_vector' => $query['vector'] !== null,
                'has_hash' => $query['hash'] !== null,
                'top' => array_slice(array_values($matches), 0, 5),
            ]);

            return $this->respond($request, $matches, $mode, $query['color']);
        } catch (Throwable $e) {
            Log::error('Visual search error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->error('حدث خطأ أثناء البحث البصري. تحقق من سجل Laravel.', 500);
        }
    }

    /* =========================================================
     |  تحليل صورة العميل
     * ========================================================= */

    /**
     * يستخرج المتجه واللون والهاش.
     * فشل Gemini لا يوقف البحث، ننتقل للهاش فقط.
     */
    private function analyzeQueryImage(string $path, GeminiImageService $gemini): array
    {
        $vector = null;
        $color = null;

        try {
            $result = $gemini->embedImage($path);
            $vector = $this->extractVector($result);
        } catch (Throwable $e) {
            Log::warning('Visual search: embedding failed', ['message' => $e->getMessage()]);
        }

        // اللون مطلوب للمرحلة 1 فقط
        if ($vector !== null) {
            try {
                $description = $gemini->describeImage($path);
                $color = $this->normalizeHex($description['main_color_hex'] ?? null);
            } catch (Throwable $e) {
                Log::warning('Visual search: color failed', ['message' => $e->getMessage()]);
            }
        }

        return [
            'vector' => $vector,
            'color' => $color,
            'hash' => $this->perceptualHash($path),
        ];
    }

    /* =========================================================
     |  المطابقة
     * ========================================================= */

    /**
     * @return array{0: array<int, array>, 1: string}
     */
    private function findMatches(array $query): array
    {
        if ($query['vector'] !== null) {
            [$stage1, $stage2] = $this->matchByFeatures($query);

            if (!empty($stage1)) {
                return [$stage1, 'embedding_color'];
            }

            if (!empty($stage2)) {
                return [$stage2, 'embedding_hash'];
            }
        }

        if ($query['hash'] !== null) {
            $stage3 = $this->matchByProductHash($query['hash']);

            if (!empty($stage3)) {
                return [$stage3, 'hash_fallback'];
            }
        }

        return [[], 'no_matches'];
    }

    /**
     * المرحلتان 1 و2 في مرور واحد على جدول الميزات.
     * النتيجة: أفضل لون لكل منتج، مرتبة تنازلياً.
     */
    private function matchByFeatures(array $query): array
    {
        $features = ProductVisualFeature::query()
            ->select(['color_id', 'image_hash', 'dominant_color', 'image_features_json'])
            ->get();

        $colorProducts = DB::table('colors')
            ->whereIn('color_id', $features->pluck('color_id'))
            ->pluck('p_id', 'color_id');

        $stage1 = [];
        $stage2 = [];

        foreach ($features as $feature) {
            $pId = $colorProducts[$feature->color_id] ?? null;
            $stored = $feature->image_features_json;

            if (!$pId || !is_array($stored) || ($stored['provider'] ?? null) !== 'gemini') {
                continue;
            }

            $embedding = $this->cosine($query['vector'], $stored['embedding'] ?? []);

            if ($embedding < self::MIN_EMBEDDING) {
                continue;
            }

            $hash = $this->hashSimilarity($query['hash'], $feature->image_hash);
            $storedColor = $this->normalizeHex($feature->dominant_color);

            // المرحلة 1: نفس اللون
            if ($query['color'] && $storedColor) {
                $color = $this->colorSimilarity($query['color'], $storedColor);
                $score = 0.82 * $embedding + 0.10 * $hash + 0.08 * $color;

                if ($color >= self::STAGE1_MIN_COLOR && $score >= self::STAGE1_MIN_SCORE) {
                    $this->keepBest($stage1, (int) $pId, (int) $feature->color_id, $score);
                }
            }

            // المرحلة 2: تجاهل اللون
            $score = 0.85 * $embedding + 0.15 * $hash;

            if ($score >= self::STAGE2_MIN_SCORE) {
                $this->keepBest($stage2, (int) $pId, (int) $feature->color_id, $score);
            }
        }

        return [$this->sortByScore($stage1), $this->sortByScore($stage2)];
    }

    /**
     * المرحلة 3: هاش الصورة الرئيسية لكل منتج.
     * 90% فأعلى أولاً ثم 80% فأعلى، الأقوى أولاً.
     */
    private function matchByProductHash(string $queryHash): array
    {
        $matches = [];

        $products = Product::query()
            ->whereNotNull('p_image')
            ->where('p_image', '!=', '')
            ->pluck('p_image', 'p_id');

        foreach ($products as $pId => $imageName) {
            $hash = $this->productImageHash($imageName);
            $score = $this->hashSimilarity($queryHash, $hash);

            if ($score >= self::HASH_MIN) {
                $matches[$pId] = [
                    'p_id' => (int) $pId,
                    'color_id' => null,
                    'score' => $score,
                    'tier' => $score >= self::HASH_STRONG ? 'strong' : 'good',
                ];
            }
        }

        return $this->sortByScore($matches);
    }

    /**
     * هاش صورة المنتج مع كاش حتى لا يُحسب في كل بحث.
     * يتجدد تلقائياً إذا تغير الملف.
     */
    private function productImageHash(string $imageName): ?string
    {
        $path = storage_path('app/public/' . self::IMAGE_DIR . basename($imageName));

        if (!is_file($path)) {
            return null;
        }

        $key = 'phash:' . md5($path . '|' . filemtime($path));

        return Cache::rememberForever($key, fn () => $this->perceptualHash($path));
    }

    private function keepBest(array &$list, int $pId, int $colorId, float $score): void
    {
        if (!isset($list[$pId]) || $score > $list[$pId]['score']) {
            $list[$pId] = [
                'p_id' => $pId,
                'color_id' => $colorId,
                'score' => $score,
            ];
        }
    }

    private function sortByScore(array $list): array
    {
        uasort($list, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $list;
    }

    /* =========================================================
     |  الاستجابة
     * ========================================================= */

    private function respond(Request $request, array $matches, string $mode, ?string $queryColor)
    {
        $page = max(1, (int) $request->input('page', 1));
        $total = count($matches);
        $lastPage = max(1, (int) ceil($total / self::PER_PAGE));

        $pageMatches = array_slice($matches, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true);

        $products = Product::query()
            ->with(['colors.images', 'discount'])
            ->whereIn('p_id', array_keys($pageMatches))
            ->get()
            ->keyBy('p_id');

        // صورة اللون المطابق إن وجد
        $colorImages = DB::table('product_images')
            ->whereIn('color_id', array_filter(array_column($pageMatches, 'color_id')))
            ->orderBy('img_id')
            ->get(['color_id', 'img_url'])
            ->unique('color_id')
            ->pluck('img_url', 'color_id');

        $data = [];

        foreach ($pageMatches as $pId => $match) {
            $product = $products[$pId] ?? null;

            if (!$product) {
                continue;
            }

            $data[] = [
                'p_id' => $product->p_id,
                'subcat_id' => $product->subcat_id,
                'p_name' => $product->p_name,
                'p_name_en' => $product->p_name_en,
                'p_price' => $product->p_price,
                'p_image' => $colorImages[$match['color_id']] ?? $product->p_image,
                'colors' => $product->colors,
                'discount' => $product->discount,
                'sizes' => [],
                'general_advertisements' => [],
                'similarity_score' => round($match['score'] * 100, 2),
                'matched_color_id' => $match['color_id'],
            ];
        }

        return response()->json([
            'status' => true,
            'message' => $total > 0
                ? 'تم العثور على المنتجات الأكثر تشابهاً.'
                : 'لم يتم العثور على منتجات مشابهة.',
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'has_more' => $page < $lastPage,
                'total' => $total,
                'seed' => (int) $request->input('seed', time()),
                'matching_mode' => $mode,
                'query_color' => $queryColor,
            ],
        ]);
    }

    private function error(string $message, int $status, $errors = null)
    {
        return response()->json(array_filter([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => [],
        ], fn ($v) => $v !== null), $status);
    }

    /* =========================================================
     |  مصدر صورة العميل
     * ========================================================= */

    /**
     * ملف مرفوع، أو رابط صورة من المتجر نفسه:
     * /api/media/uploads/products/x.jpg
     * /storage/uploads/products/x.jpg
     * uploads/products/x.jpg
     */
    private function resolveQueryImage(Request $request): ?string
    {
        $file = $request->file('image');

        if ($file && $file->isValid()) {
            return $this->validImage($file->getRealPath());
        }

        $url = trim((string) $request->input('image_url', ''));

        if ($url === '' || str_starts_with($url, 'blob:')) {
            return null;
        }

        $path = urldecode((string) parse_url($url, PHP_URL_PATH));
        $pos = strpos($path, self::IMAGE_DIR);

        if ($pos === false) {
            return null;
        }

        $name = basename(substr($path, $pos + strlen(self::IMAGE_DIR)));

        if ($name === '' || str_contains($name, '..')) {
            return null;
        }

        return $this->validImage(storage_path('app/public/' . self::IMAGE_DIR . $name));
    }

    private function validImage(?string $path): ?string
    {
        return $path && is_file($path) && @getimagesize($path) ? $path : null;
    }

    /* =========================================================
     |  دوال التشابه
     * ========================================================= */

    private function extractVector($result): ?array
    {
        $vector = $result['embedding'] ?? $result['values'] ?? $result;

        if (!is_array($vector) || !array_is_list($vector) || count($vector) < 64) {
            return null;
        }

        return array_map('floatval', $vector);
    }

    private function cosine(array $a, $b): float
    {
        if (!is_array($b) || count($a) !== count($b) || empty($a)) {
            return 0.0;
        }

        $dot = $normA = $normB = 0.0;

        foreach ($a as $i => $x) {
            $y = (float) $b[$i];
            $dot += $x * $y;
            $normA += $x * $x;
            $normB += $y * $y;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return max(0.0, min(1.0, $dot / (sqrt($normA) * sqrt($normB))));
    }

    /**
     * نسبة البتات المتطابقة بين هاشين (0 إلى 1).
     */
    private function hashSimilarity(?string $a, ?string $b): float
    {
        if (!$a || !$b || strlen($a) !== strlen($b) || !ctype_xdigit($a . $b)) {
            return 0.0;
        }

        $bytesA = hex2bin($a);
        $bytesB = hex2bin($b);
        $diff = 0;

        for ($i = 0, $n = strlen($bytesA); $i < $n; $i++) {
            $diff += substr_count(decbin(ord($bytesA[$i]) ^ ord($bytesB[$i])), '1');
        }

        return 1.0 - $diff / (strlen($bytesA) * 8);
    }

    private function colorSimilarity(string $a, string $b): float
    {
        [$r1, $g1, $b1] = sscanf($a, '#%02x%02x%02x');
        [$r2, $g2, $b2] = sscanf($b, '#%02x%02x%02x');

        $distance = sqrt(($r1 - $r2) ** 2 + ($g1 - $g2) ** 2 + ($b1 - $b2) ** 2);

        // 441.67 أقصى مسافة بين لونين
        return max(0.0, 1.0 - $distance / 441.673);
    }

    private function normalizeHex($value): ?string
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-fA-F]{6}$/', trim($value))) {
            return null;
        }

        return strtoupper(trim($value));
    }

    /**
     * DCT perceptual hash (64 بت).
     * يجب أن تبقى مطابقة للخوارزمية في ProcessImageWithGoogleVision.
     */
    private function perceptualHash(string $path): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($path);

        $image = match ($info['mime'] ?? '') {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (!$image) {
            return null;
        }

        $n = 32;
        $thumb = imagecreatetruecolor($n, $n);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $n, $n, imagesx($image), imagesy($image));
        imagedestroy($image);

        $gray = [];

        for ($y = 0; $y < $n; $y++) {
            for ($x = 0; $x < $n; $x++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $gray[$y][$x] = 0.299 * (($rgb >> 16) & 255)
                    + 0.587 * (($rgb >> 8) & 255)
                    + 0.114 * ($rgb & 255);
            }
        }

        imagedestroy($thumb);

        // جدول جيب التمام مرة واحدة لتسريع الحساب
        $cos = [];

        for ($k = 0; $k < 8; $k++) {
            for ($i = 0; $i < $n; $i++) {
                $cos[$k][$i] = cos(((2 * $i + 1) * $k * M_PI) / (2 * $n));
            }
        }

        $coef = [];

        for ($v = 0; $v < 8; $v++) {
            for ($u = 0; $u < 8; $u++) {
                $sum = 0.0;

                for ($y = 0; $y < $n; $y++) {
                    $row = 0.0;

                    for ($x = 0; $x < $n; $x++) {
                        $row += $gray[$y][$x] * $cos[$u][$x];
                    }

                    $sum += $row * $cos[$v][$y];
                }

                $cu = $u === 0 ? sqrt(1 / $n) : sqrt(2 / $n);
                $cv = $v === 0 ? sqrt(1 / $n) : sqrt(2 / $n);
                $coef[] = $sum * $cu * $cv;
            }
        }

        // استبعاد معامل DC
        $low = array_slice($coef, 1);
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