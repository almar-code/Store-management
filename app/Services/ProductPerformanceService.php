<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * تحليل أداء المنتجات: البيانات، المؤشرات، نسبة الأداء، التصنيف، التشخيص، الحفظ
 */
class ProductPerformanceService
{
    // حدود حالة قيد التقييم
    public const MIN_AGE_DAYS = 7;
    public const MIN_VIEWS = 20;

    // أقل عدد منتجات مؤهلة لتشغيل K-Means
    public const MIN_PRODUCTS_FOR_CLUSTERING = 9;

    // وزن كل مؤشر في نسبة الأداء (المجموع = 1)
    public const WEIGHTS = [
        'sales_velocity'  => 0.30,
        'conversion_rate' => 0.25,
        'cart_rate'       => 0.15,
        'engagement_rate' => 0.15,
        'satisfaction'    => 0.15,
    ];

    /**
     * تشغيل التحليل كاملاً وحفظ نتيجة اليوم
     * يرجع عدد المنتجات في كل حالة
     */
    public static function run(): array
    {
        $products = self::analyze();
        $products = self::classify($products);
        $products = self::diagnose($products);

        self::save($products);

        return $products->countBy('status')->all();
    }

    /**
     * البيانات ثم المؤشرات ثم نسبة الأداء
     */
    public static function analyze(): Collection
    {
        $products = self::collectData()->map(fn ($row) => self::buildProduct($row));

        // نسبة الأداء تُحسب للمنتجات المؤهلة فقط
        [$scores, $ranks] = self::calculateScores($products->where('eligible', true));

        return $products->map(function ($product) use ($scores, $ranks) {
            $product['score'] = $scores[$product['p_id']] ?? null;
            $product['ranks'] = $ranks[$product['p_id']] ?? null;
            return $product;
        });
    }

    /**
     * جلب كل المنتجات مع قسمها وخصمها وعداداتها في استعلام واحد
     */
    public static function collectData(): Collection
    {
        $today = now()->toDateString();

        // الخصم الساري للمنتج
        $discount = DB::table('discounts')
            ->select('discount_perce')
            ->whereColumn('discounts.p_id', 'products.p_id')
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->orderByDesc('created_at')
            ->limit(1);

        return DB::table('products')
            ->join('subcategories', 'products.subcat_id', '=', 'subcategories.subcat_id')
            ->join('categories', 'subcategories.cat_id', '=', 'categories.cat_id')
            ->leftJoin('product_stats', 'products.p_id', '=', 'product_stats.p_id')
            ->select(
                'products.p_id',
                'products.p_name',
                'products.p_image',
                'products.p_price',
                'products.created_at',
                'categories.cat_id',
                'categories.cat_name',
                DB::raw('COALESCE(product_stats.views_count, 0) AS views'),
                DB::raw('COALESCE(product_stats.cart_adds_count, 0) AS cart_adds'),
                DB::raw('COALESCE(product_stats.favorites_count, 0) AS favorites'),
                DB::raw('COALESCE(product_stats.shares_count, 0) AS shares'),
                DB::raw('COALESCE(product_stats.sales_count, 0) AS sales'),
                DB::raw('COALESCE(product_stats.positive_comments_count, 0) AS positive_comments'),
                DB::raw('COALESCE(product_stats.negative_comments_count, 0) AS negative_comments')
            )
            ->selectSub($discount, 'discount')
            ->get();
    }

    /**
     * تجهيز بيانات منتج واحد مع مؤشراته
     */
    private static function buildProduct(object $row): array
    {
        // عمر المنتج بالأيام (أقل قيمة 1 لتجنب القسمة على صفر)
        $ageDays = max(1, (int) floor(Carbon::parse($row->created_at)->diffInDays(now())));

        $views = (int) $row->views;
        $sales = (int) $row->sales;
        $positive = (int) $row->positive_comments;
        $negative = (int) $row->negative_comments;
        $comments = $positive + $negative;

        $indicators = [
            // مبيعات اليوم الواحد
            'sales_velocity'  => round($sales / $ageDays, 4),
            // من شاهد ثم اشترى
            'conversion_rate' => $views > 0 ? round($sales / $views, 4) : 0,
            // من شاهد ثم أضاف للسلة
            'cart_rate'       => $views > 0 ? round($row->cart_adds / $views, 4) : 0,
            // من شاهد ثم أضاف للمفضلة أو شارك
            'engagement_rate' => $views > 0 ? round(($row->favorites + $row->shares) / $views, 4) : 0,
            // رضا العملاء من -1 إلى 1 (صفر عند عدم وجود تعليقات)
            'satisfaction'    => $comments > 0 ? round(($positive - $negative) / $comments, 4) : 0,
        ];

        return [
            'p_id'              => $row->p_id,
            'name'              => $row->p_name,
            'image'             => $row->p_image,
            'price'             => (float) $row->p_price,
            'discount'          => (int) ($row->discount ?? 0),
            'category_id'       => $row->cat_id,
            'category'          => $row->cat_name,
            'age_days'          => $ageDays,
            'views'             => $views,
            'cart_adds'         => (int) $row->cart_adds,
            'favorites'         => (int) $row->favorites,
            'shares'            => (int) $row->shares,
            'sales'             => $sales,
            'positive_comments' => $positive,
            'negative_comments' => $negative,
            'indicators'        => $indicators,
            // هل البيانات كافية للحكم على المنتج
            'eligible'          => $ageDays >= self::MIN_AGE_DAYS && $views >= self::MIN_VIEWS,
        ];
    }

    /**
     * نسبة الأداء من 100 لكل منتج مؤهل
     * يرجع: [نسب الأداء, ترتيب كل مؤشر من 0 إلى 1 لاستخدامه في K-Means]
     */
    private static function calculateScores(Collection $products): array
    {
        if ($products->isEmpty()) {
            return [[], []];
        }

        $scores = array_fill_keys($products->pluck('p_id')->all(), 0);
        $ranks = [];

        foreach (self::WEIGHTS as $indicator => $weight) {
            $values = $products->pluck("indicators.$indicator", 'p_id')->all();

            foreach ($values as $productId => $value) {
                $rank = self::percentile($value, $values);
                $ranks[$productId][] = round($rank / 100, 4);
                $scores[$productId] += $weight * $rank;
            }
        }

        return [array_map(fn ($score) => round($score, 2), $scores), $ranks];
    }

    /**
     * الترتيب المئوي لقيمة بين مجموعة قيم (من 0 إلى 100)
     */
    private static function percentile(float $value, array $values): float
    {
        $below = count(array_filter($values, fn ($v) => $v < $value));
        $equal = count(array_filter($values, fn ($v) => $v == $value));

        return (($below + 0.5 * $equal) / count($values)) * 100;
    }

    /**
     * تحديد حالة كل منتج: عالي / طبيعي / راكد / قيد التقييم
     */
    private static function classify(Collection $products): Collection
    {
        $eligible = $products->where('eligible', true);
        $results = []; // رقم المنتج => [الحالة, المجموعة]

        if ($eligible->count() >= self::MIN_PRODUCTS_FOR_CLUSTERING) {
            // التصنيف بـ K-Means على ترتيب المؤشرات
            $labels = KMeansService::cluster($eligible->pluck('ranks', 'p_id')->all(), 3)['labels'];
            $scores = $eligible->pluck('score', 'p_id');

            // ترتيب المجموعات من الأضعف للأقوى حسب متوسط نسبة الأداء
            $order = collect($labels)
                ->map(fn ($cluster, $productId) => ['cluster' => $cluster, 'score' => $scores[$productId]])
                ->groupBy('cluster')
                ->map(fn ($group) => $group->avg('score'))
                ->sort()
                ->keys()
                ->values();

            // تسمية المجموعات: الأضعف راكد والأقوى عالي والبقية طبيعي
            $names = [];
            $positions = [];
            foreach ($order as $position => $cluster) {
                if ($order->count() === 1) {
                    $names[$cluster] = 'normal';
                } elseif ($position === 0) {
                    $names[$cluster] = 'stagnant';
                } elseif ($position === $order->count() - 1) {
                    $names[$cluster] = 'high';
                } else {
                    $names[$cluster] = 'normal';
                }
                $positions[$cluster] = $position;
            }

            foreach ($labels as $productId => $cluster) {
                $results[$productId] = [$names[$cluster], $positions[$cluster]];
            }
        } else {
            // منتجات قليلة: التصنيف حسب نسبة الأداء فقط
            foreach ($eligible as $product) {
                $status = $product['score'] >= 67 ? 'high' : ($product['score'] >= 34 ? 'normal' : 'stagnant');
                $results[$product['p_id']] = [$status, null];
            }
        }

        return $products->map(function ($product) use ($results) {
            [$product['status'], $product['cluster']] = $results[$product['p_id']] ?? ['pending', null];
            return $product;
        });
    }

    /**
     * تحديد سبب حالة كل منتج بمقارنته بمتوسط المتجر
     */
    private static function diagnose(Collection $products): Collection
    {
        $eligible = $products->where('eligible', true);

        // متوسطات المتجر للمقارنة
        $averages = [
            'views'     => $eligible->avg('views') ?: 0,
            'cart_rate' => $eligible->avg('indicators.cart_rate') ?: 0,
            'checkout'  => $eligible->avg(fn ($p) => self::checkoutRate($p)) ?: 0,
        ];

        return $products->map(function ($product) use ($averages) {
            $product['diagnosis'] = self::diagnosisFor($product, $averages);
            return $product;
        });
    }

    /**
     * رمز سبب الحالة (نصوصه وتوصياته في config/product_performance.php)
     */
    private static function diagnosisFor(array $product, array $averages): string
    {
        if (!$product['eligible']) {
            return 'insufficient_data';
        }
        if ($product['status'] === 'high') {
            return 'top_performer';
        }
        // تعليقات سلبية أكثر من الإيجابية
        if ($product['negative_comments'] > $product['positive_comments'] && $product['negative_comments'] >= 2) {
            return 'negative_feedback';
        }
        // مشاهدات أقل من نصف متوسط المتجر
        if ($product['views'] < $averages['views'] * 0.5) {
            return 'low_visibility';
        }
        // إضافة للسلة أقل من نصف متوسط المتجر
        if ($product['indicators']['cart_rate'] < $averages['cart_rate'] * 0.5) {
            return 'weak_appeal';
        }
        // يُضاف للسلة لكن نادراً ما يُشترى
        if ($product['cart_adds'] > 0 && self::checkoutRate($product) < $averages['checkout'] * 0.5) {
            return 'checkout_drop';
        }

        return $product['status'] === 'stagnant' ? 'low_sales' : 'steady';
    }

    /**
     * نسبة من أضاف للسلة ثم اشترى
     */
    private static function checkoutRate(array $product): float
    {
        return $product['cart_adds'] > 0 ? $product['sales'] / $product['cart_adds'] : 0;
    }

    /**
     * حفظ نتيجة اليوم (تحديث صف اليوم إن كان موجوداً)
     */
    private static function save(Collection $products): void
    {
        $today = now()->toDateString();
        $now = now();

        $rows = $products->map(fn ($p) => [
            'p_id'          => $p['p_id'],
            'analysis_date' => $today,
            'status'        => $p['status'],
            'score'         => $p['score'] ?? 0,
            'cluster'       => $p['cluster'],
            'diagnosis'     => $p['diagnosis'],
            'views'         => $p['views'],
            'cart_adds'     => $p['cart_adds'],
            'favorites'     => $p['favorites'],
            'shares'        => $p['shares'],
            'sales'         => $p['sales'],
            'indicators'    => json_encode($p['indicators']),
            'created_at'    => $now,
            'updated_at'    => $now,
        ])->values()->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_performance')->upsert(
                $chunk,
                ['p_id', 'analysis_date'],
                ['status', 'score', 'cluster', 'diagnosis', 'views', 'cart_adds', 'favorites', 'shares', 'sales', 'indicators', 'updated_at']
            );
        }
    }
}