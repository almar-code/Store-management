<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVisualFeature;
use App\Services\GeminiImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class VisualSearchController extends Controller
{
    // قيم بداية تحتاج معايرة على صور المنتجات الحقيقية.
    private const MATCH_THRESHOLD = 0.68;
    private const MIN_EMBEDDING_SCORE = 0.78;

    public function getVisualSearchProducts(
        Request $request,
        GeminiImageService $gemini
    ) {
        set_time_limit(300);

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_url' => 'nullable|string|max:2048',
            'page' => 'nullable|integer|min:1',
            'seed' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات البحث غير صحيحة.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $imageFile = $request->file('image');
        $imageUrl = trim((string) $request->input('image_url', ''));
        $tempPath = null;

        try {
            if ($imageFile && $imageFile->isValid()) {
                $realPath = $imageFile->getRealPath();
            } elseif ($imageUrl !== '') {
                $realPath = $this->resolveImageUrl($imageUrl, $tempPath);
            } else {
                $realPath = null;
            }

            if (
                !$realPath
                || !is_file($realPath)
                || !@getimagesize($realPath)
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'تعذر الوصول إلى صورة صالحة. أرسل image أو image_url صالحاً.',
                    'data' => [],
                ], 422);
            }

            // 1. استخراج تضمين الصورة المرفوعة.
            $uploaded = $gemini->embedImage($realPath);

            $queryVector = $uploaded['embedding']
                ?? $uploaded['values']
                ?? (array_is_list($uploaded) ? $uploaded : null);

            if (!is_array($queryVector) || count($queryVector) < 64) {
                throw new \RuntimeException(
                    'Gemini لم يرجع متجهاً بصرياً صالحاً.'
                );
            }

            $queryVector = array_values(array_map('floatval', $queryVector));

            // 2. استخراج اللون مرة واحدة فقط لكل بحث، وليس داخل حلقة النتائج.
            // يجب أن تُرجع describeImage الحقل main_color_hex.
            $queryColor = null;

            try {
                
$queryDescription = $gemini->describeImage($realPath);

Log::info('Gemini Query Description Debug', [
    'description' => $queryDescription,
    'main_color' => is_array($queryDescription)
        ? ($queryDescription['main_color'] ?? null)
        : null,
    'main_color_hex' => is_array($queryDescription)
        ? ($queryDescription['main_color_hex'] ?? null)
        : null,
]);

$candidateColor = is_array($queryDescription)
    ? ($queryDescription['main_color_hex'] ?? null)
    : null;

if (
    is_string($candidateColor)
    && preg_match('/^#[0-9a-fA-F]{6}$/', trim($candidateColor))
) {
    $queryColor = strtoupper(trim($candidateColor));
} else {
    $queryColor = null;
}

            } catch (Throwable $colorException) {
                // فشل استخراج اللون لا يمنع البحث بالتضمين والبصمة.
                Log::warning('Could not extract query garment color', [
                    'message' => $colorException->getMessage(),
                ]);
            }

            // 3. حساب البصمة الإدراكية للصورة المرفوعة.
            $queryHash = $this->calculatePerceptualHash($realPath);

            // 4. قراءة الخصائص المفهرسة مسبقاً.
            $features = ProductVisualFeature::query()
                ->whereNotNull('color_id')
                ->get();

            $matches = [];

            foreach ($features as $feature) {
                $stored = $feature->image_features_json;

                if (is_string($stored)) {
                    $stored = json_decode($stored, true);
                }

                // لا نقارن بيانات Google Vision القديمة بتضمينات Gemini.
                if (
                    !is_array($stored)
                    || ($stored['provider'] ?? null) !== 'gemini'
                    || !is_array($stored['embedding'] ?? null)
                    || count($stored['embedding']) !== count($queryVector)
                ) {
                    continue;
                }

                $embeddingScore = $this->cosineSimilarity(
                    $queryVector,
                    $stored['embedding']
                );

                // ارفض النتائج ذات التشابه البصري الدلالي المنخفض.
                if ($embeddingScore < self::MIN_EMBEDDING_SCORE) {
                    continue;
                }

                $hashScore = $this->hashSimilarity(
                    $queryHash,
                    $feature->image_hash
                );

                $colorScore = $this->colorSimilarity(
                    $queryColor,
                    $feature->dominant_color
                );

                /*
                 * عندما يتوفر لون من Gemini:
                 *   embedding 82%، hash 10%، color 8%.
                 *
                 * عندما لا يتوفر لون صالح:
                 *   نعيد توزيع الوزن المتاح بين embedding وhash
                 *   بدلاً من معاقبة النتيجة بسبب لون غير معروف.
                 */
                if ($queryColor !== null && $feature->dominant_color) {
                    $score =
                        (0.82 * $embeddingScore)
                        + (0.10 * $hashScore)
                        + (0.08 * $colorScore);
                } else {
                    $score =
                        (0.82 / 0.92 * $embeddingScore)
                        + (0.10 / 0.92 * $hashScore);
                }

                Log::info('Visual Search Match Debug', [
                    'color_id' => $feature->color_id,
                    'stored_hash' => $feature->image_hash,
                    'uploaded_hash' => $queryHash,
                    'query_color' => $queryColor,
                    'stored_color' => $feature->dominant_color,
                    'hash_score' => round($hashScore * 100, 2),
                    'color_score' => round($colorScore * 100, 2),
                    'embedding_score' => round($embeddingScore * 100, 2),
                    'final_score' => round($score, 4),
                    'threshold' => self::MATCH_THRESHOLD,
                    'minimum_embedding_score' => self::MIN_EMBEDDING_SCORE,
                ]);

                if ($score < self::MATCH_THRESHOLD) {
                    continue;
                }

                $color = DB::table('colors')
                    ->where('color_id', $feature->color_id)
                    ->first();

                if (!$color) {
                    continue;
                }

                $colorImage = DB::table('product_images')
                    ->where('color_id', $feature->color_id)
                    ->orderBy('img_id')
                    ->value('img_url');

                $pId = (int) $color->p_id;

                if (
                    !isset($matches[$pId])
                    || $score > $matches[$pId]['raw_score']
                ) {
                    $matches[$pId] = [
                        'p_id' => $pId,
                        'color_id' => (int) $color->color_id,
                        'color_image' => $colorImage,
                        'score' => round($score * 100, 2),
                        'raw_score' => $score,
                    ];
                }
            }

            // ترتيب المنتجات من الأكثر تشابهاً إلى الأقل.
            uasort(
                $matches,
                fn ($a, $b) => $b['raw_score'] <=> $a['raw_score']
            );

            $query = Product::query()
                ->with(['colors.images', 'discount'])
                ->select([
                    'products.p_id',
                    'products.subcat_id',
                    'products.p_name',
                    'products.p_name_en',
                    'products.p_price',
                    'products.p_image',
                ]);

            if (!empty($matches)) {
                $ids = array_map('intval', array_keys($matches));
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $query->whereIn('products.p_id', $ids)
                    ->orderByRaw(
                        "FIELD(products.p_id, {$placeholders})",
                        $ids
                    );
            } else {
                // لا نعرض منتجات عشوائية وكأنها نتائج مطابقة.
                $query->whereRaw('1 = 0');
            }

            $page = max(1, (int) $request->input('page', 1));
            $products = $query->paginate(10, ['*'], 'page', $page);

            $formatted = collect($products->items())
                ->map(function ($product) use ($matches) {
                    $details = $matches[$product->p_id] ?? null;
                    $selectedImage = $details['color_image']
                        ?? $product->p_image;

                    if (
                        empty($selectedImage)
                        && $product->colors->isNotEmpty()
                        && $product->colors->first()->images->isNotEmpty()
                    ) {
                        $selectedImage = $product->colors
                            ->first()->images
                            ->first()->img_url;
                    }

                    return [
                        'p_id' => $product->p_id,
                        'subcat_id' => $product->subcat_id,
                        'p_name' => $product->p_name,
                        'p_name_en' => $product->p_name_en,
                        'p_price' => $product->p_price,
                        'p_image' => $selectedImage,
                        'colors' => $product->colors,
                        'discount' => $product->discount,
                        'sizes' => [],
                        'general_advertisements' => [],
                        'similarity_score' => $details['score'] ?? null,
                        'matched_color_id' => $details['color_id'] ?? null,
                    ];
                })
                ->values();

            return response()->json([
                'status' => true,
                'message' => !empty($matches)
                    ? 'تم العثور على المنتجات الأكثر تشابهاً.'
                    : 'لم يتم العثور على منتجات مطابقة بدرجة كافية. تحقق من فهرسة صور المنتجات.',
                'data' => $formatted,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'has_more' => $products->hasMorePages(),
                    'total' => $products->total(),
                    'seed' => (int) $request->input('seed', time()),
                    'matching_mode' => !empty($matches)
                        ? 'gemini_embedding'
                        : 'no_confident_matches',
                    'query_color' => $queryColor,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Gemini visual search error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء البحث البصري. تحقق من سجل Laravel.',
                'data' => [],
            ], 500);
        } finally {
            if ($tempPath && is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    private function resolveImageUrl(
        string $url,
        ?string &$tempPath
    ): ?string {
        $tempPath = null;

        if (str_starts_with($url, 'blob:')) {
            throw new \InvalidArgumentException(
                'رابط blob لا يصلح للسيرفر؛ أرسل الصورة كملف multipart.'
            );
        }

        $parsed = parse_url($url);

        if (!$parsed) {
            return null;
        }

        $path = urldecode($parsed['path'] ?? '');

        // روابط صور المنتجات المحلية.
        if (str_contains($path, '/storage/')) {
            $relative = substr(
                $path,
                strpos($path, '/storage/') + strlen('/storage/')
            );

            if (
                str_contains($relative, '..')
                || !str_starts_with($relative, 'uploads/products/')
            ) {
                return null;
            }

            $local = public_path('storage/' . $relative);

            return is_file($local) ? $local : null;
        }

        if (!isset($parsed['scheme']) && str_starts_with(
            $path,
            'uploads/products/'
        )) {
            if (str_contains($path, '..')) {
                return null;
            }

            $local = public_path('storage/' . $path);

            return is_file($local) ? $local : null;
        }

        // السماح بجلب الصور من نطاق التطبيق فقط.
        if (
            strtolower($parsed['scheme'] ?? '') !== 'https'
            || empty($parsed['host'])
        ) {
            return null;
        }

        $allowed = array_filter([
            parse_url(config('app.url'), PHP_URL_HOST),
        ]);

        if (!in_array(strtolower($parsed['host']), $allowed, true)) {
            return null;
        }

        $response = Http::timeout(15)
            ->withOptions(['allow_redirects' => false])
            ->get($url);

        if (
            !$response->successful()
            || strlen($response->body()) > 10 * 1024 * 1024
        ) {
            return null;
        }

        $mime = strtolower(trim(
            explode(';', $response->header('Content-Type') ?? '')[0]
        ));

        if (!in_array($mime, [
            'image/jpeg',
            'image/png',
            'image/webp',
        ], true)) {
            return null;
        }

        $ext = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $dir = storage_path('app/temp');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tempPath = $dir . DIRECTORY_SEPARATOR
            . uniqid('visual_', true) . '.' . $ext;

        file_put_contents($tempPath, $response->body());

        return $tempPath;
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || empty($a)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $v) {
            $x = (float) $v;
            $y = (float) $b[$i];

            $dot += $x * $y;
            $normA += $x * $x;
            $normB += $y * $y;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return max(
            0.0,
            min(1.0, $dot / (sqrt($normA) * sqrt($normB)))
        );
    }

    private function hashSimilarity(?string $a, ?string $b): float
    {
        if (
            !$a || !$b
            || strlen($a) !== strlen($b)
            || !ctype_xdigit($a)
            || !ctype_xdigit($b)
        ) {
            return 0.0;
        }

        $bytesA = hex2bin($a);
        $bytesB = hex2bin($b);

        if (
            $bytesA === false
            || $bytesB === false
            || strlen($bytesA) !== strlen($bytesB)
        ) {
            return 0.0;
        }

        $different = 0;

        for ($i = 0; $i < strlen($bytesA); $i++) {
            $different += substr_count(
                decbin(ord($bytesA[$i]) ^ ord($bytesB[$i])),
                '1'
            );
        }

        $total = strlen($bytesA) * 8;

        return max(0.0, 1.0 - ($different / $total));
    }

    private function colorSimilarity(?string $a, ?string $b): float
    {
        if (
            !$a || !$b
            || !preg_match('/^#[0-9a-fA-F]{6}$/', $a)
            || !preg_match('/^#[0-9a-fA-F]{6}$/', $b)
        ) {
            return 0.0;
        }

        $c1 = sscanf($a, '#%02x%02x%02x');
        $c2 = sscanf($b, '#%02x%02x%02x');

        if (
            count($c1) !== 3
            || count($c2) !== 3
            || in_array(false, $c1, true)
            || in_array(false, $c2, true)
        ) {
            return 0.0;
        }

        $distance = sqrt(
            ($c1[0] - $c2[0]) ** 2
            + ($c1[1] - $c2[1]) ** 2
            + ($c1[2] - $c2[2]) ** 2
        );

        return max(0.0, 1.0 - $distance / 441.673);
    }

    /**
     * يجب أن تتطابق هذه الخوارزمية مع خوارزمية الفهرسة في الـ Job.
     */
    private function calculatePerceptualHash(string $path): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($path);

        if (!$info) {
            return null;
        }

        $image = match ($info['mime'] ?? '') {
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

        $n = 32;
        $thumb = imagecreatetruecolor($n, $n);

        imagecopyresampled(
            $thumb,
            $image,
            0,
            0,
            0,
            0,
            $n,
            $n,
            imagesx($image),
            imagesy($image)
        );

        $gray = [];

        for ($y = 0; $y < $n; $y++) {
            for ($x = 0; $x < $n; $x++) {
                $rgb = imagecolorat($thumb, $x, $y);

                $gray[$y][$x] =
                    0.299 * (($rgb >> 16) & 255)
                    + 0.587 * (($rgb >> 8) & 255)
                    + 0.114 * ($rgb & 255);
            }
        }

        imagedestroy($image);
        imagedestroy($thumb);

        $coef = [];

        for ($v = 0; $v < 8; $v++) {
            for ($u = 0; $u < 8; $u++) {
                $sum = 0.0;

                for ($y = 0; $y < $n; $y++) {
                    for ($x = 0; $x < $n; $x++) {
                        $sum += $gray[$y][$x]
                            * cos(((2 * $x + 1) * $u * M_PI) / (2 * $n))
                            * cos(((2 * $y + 1) * $v * M_PI) / (2 * $n));
                    }
                }

                $cu = $u === 0 ? sqrt(1 / $n) : sqrt(2 / $n);
                $cv = $v === 0 ? sqrt(1 / $n) : sqrt(2 / $n);

                $coef[] = $sum * $cu * $cv;
            }
        }

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
