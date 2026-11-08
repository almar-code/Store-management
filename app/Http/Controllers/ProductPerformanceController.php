<?php

namespace App\Http\Controllers;

use App\Services\ProductPerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductPerformanceController extends Controller
{
    // صفحة أداء المنتجات
    public function index(Request $request)
    {
        return view('Products.productperformance', $this->dashboardData($request));
    }

    // زر تحديث التحليل
    public function refresh()
    {
        ProductPerformanceService::run();

        return back()->with('success', 'تم تحديث تحليل أداء المنتجات');
    }

    // التقرير الذكي عبر n8n
    public function aiReport(Request $request)
    {
        $data = $this->dashboardData($request);

        try {
            $response = Http::timeout(120)->post(
                config('services.n8n.performance_webhook'),
                $this->reportPayload($data)
            );

            $report = $response->json('report');

            if (!$response->successful() || empty($report)) {
                Log::warning('n8n performance report failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم استلام التقرير، حاول مرة أخرى',
                ], 502);
            }

            // تنظيف علامات markdown والسماح بوسوم التنسيق فقط
            $report = preg_replace('/```(html)?/i', '', $report);
            $report = strip_tags($report, '<h3><h4><p><ul><ol><li><strong><b><br><span>');

            // حفظ التقرير لنفس يوم التحليل
            Cache::put('performance_report_' . $data['analysisDate'], $report, now()->addDays(2));

            return response()->json(['success' => true, 'report' => $report]);
        } catch (Throwable $e) {
            Log::error('Performance report error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'تعذر الاتصال بخدمة التقارير',
            ], 500);
        }
    }

    /**
     * كل بيانات الصفحة والمخططات
     */
    public function dashboardData(Request $request): array
    {
        // آخر يوم تحليل (أول مرة: تشغيل التحليل مباشرة)
        $date = DB::table('product_performance')->max('analysis_date');
        if (!$date) {
            ProductPerformanceService::run();
            $date = DB::table('product_performance')->max('analysis_date') ?? now()->toDateString();
        }

        $latest = $this->latestQuery($date)->get();
        $rated = $latest->where('status', '!=', 'pending');

        return [
            'analysisDate'  => $date,
            'lastUpdate'    => $latest->max('updated_at'),
            'usedKMeans'    => $rated->whereNotNull('cluster')->isNotEmpty(),
            'statuses'      => config('product_performance.statuses'),
            'diagnoses'     => config('product_performance.diagnoses'),
            'cards'         => $this->cards($latest, $rated),
            'topProducts'   => $rated->sortByDesc('score')->take(10)->values(),
            'worstProducts' => $rated->sortBy('score')->take(10)->values(),
            'products'      => $this->productsTable($request, $date),
            'categories'    => $latest->pluck('cat_name', 'cat_id')->unique(),
            'aiReport'      => Cache::get('performance_report_' . $date),
            'charts'        => [
                'statuses'   => $latest->countBy('status'),
                'trend'      => $this->trend(),
                'sizes'      => $this->topSizes(),
                'colors'     => $this->topColors(),
                'categories' => $this->categoriesPerformance($rated),
                'funnel'     => [
                    'views'     => $latest->sum('views'),
                    'cart_adds' => $latest->sum('cart_adds'),
                    'sales'     => $latest->sum('sales'),
                ],
                'clusters'   => $rated->map(fn ($p) => [
                    'x'         => $p->views,
                    'y'         => (float) $p->score,
                    'name'      => $p->p_name,
                    'status'    => $p->status,
                    'sales'     => $p->sales,
                    'cart_adds' => $p->cart_adds,
                ])->values(),
            ],
        ];
    }

    /**
     * نتائج يوم معين مع اسم المنتج وقسمه
     */
    private function latestQuery(string $date)
    {
        return DB::table('product_performance as pp')
            ->join('products as p', 'pp.p_id', '=', 'p.p_id')
            ->join('subcategories as s', 'p.subcat_id', '=', 's.subcat_id')
            ->join('categories as c', 's.cat_id', '=', 'c.cat_id')
            ->where('pp.analysis_date', $date)
            ->select('pp.*', 'p.p_name', 'p.p_image', 'p.p_price', 'c.cat_id', 'c.cat_name');
    }

    /**
     * أرقام البطاقات العلوية
     */
    private function cards(Collection $latest, Collection $rated): array
    {
        $views = $latest->sum('views');
        $topSize = $this->topSizes()->first();
        $topColor = $this->topColors()->first();

        return [
            'total'          => $latest->count(),
            'high'           => $latest->where('status', 'high')->count(),
            'normal'         => $latest->where('status', 'normal')->count(),
            'stagnant'       => $latest->where('status', 'stagnant')->count(),
            'pending'        => $latest->where('status', 'pending')->count(),
            // نسبة المنتجات عالية الأداء من المنتجات المقيّمة
            'high_ratio'     => $rated->count() ? round($latest->where('status', 'high')->count() / $rated->count() * 100, 1) : 0,
            // نسبة من شاهد ثم اشترى على مستوى المتجر
            'conversion'     => $views ? round($latest->sum('sales') / $views * 100, 1) : 0,
            'top_size'       => $topSize->name ?? '-',
            'top_color'      => $topColor->name ?? '-',
            'top_color_code' => $topColor->code ?? '#e5e7eb',
        ];
    }

    /**
     * جدول المنتجات مع الفلترة (10 في كل صفحة)
     */
    private function productsTable(Request $request, string $date)
    {
        return $this->latestQuery($date)
            ->when($request->status, fn ($q, $status) => $q->where('pp.status', $status))
            ->when($request->category, fn ($q, $category) => $q->where('c.cat_id', $category))
            ->orderByDesc('pp.score')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * تطور الأداء آخر 30 يوماً: المبيعات والمشاهدات اليومية وعدد المنتجات العالية والراكدة
     */
    private function trend(): Collection
    {
        $days = DB::table('product_performance')
            ->where('analysis_date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('analysis_date')
            ->orderBy('analysis_date')
            ->select(
                'analysis_date',
                DB::raw('SUM(views) AS views'),
                DB::raw('SUM(sales) AS sales'),
                DB::raw("SUM(status = 'high') AS high"),
                DB::raw("SUM(status = 'stagnant') AS stagnant")
            )
            ->get();

        // العدادات تراكمية: نشاط اليوم = الفرق عن اليوم السابق
        return $days->map(function ($day, $i) use ($days) {
            $previous = $days[$i - 1] ?? null;

            return [
                'date'     => $day->analysis_date,
                'views'    => $previous ? max(0, $day->views - $previous->views) : 0,
                'sales'    => $previous ? max(0, $day->sales - $previous->sales) : 0,
                'high'     => (int) $day->high,
                'stagnant' => (int) $day->stagnant,
            ];
        });
    }

    /**
     * المقاسات الأكثر مبيعاً من الطلبات
     */
    private function topSizes(): Collection
    {
        return DB::table('order_items')
            ->join('sizes', 'order_items.size_id', '=', 'sizes.size_id')
            ->groupBy('sizes.size_name')
            ->select('sizes.size_name AS name', DB::raw('SUM(order_items.quantity) AS total'))
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }

    /**
     * الألوان الأكثر طلباً من الطلبات مع كود اللون
     */
    private function topColors(): Collection
    {
        return DB::table('order_items')
            ->join('colors', 'order_items.color_id', '=', 'colors.color_id')
            ->groupBy('colors.color_name')
            ->select(
                'colors.color_name AS name',
                DB::raw('MAX(colors.color_code) AS code'),
                DB::raw('SUM(order_items.quantity) AS total')
            )
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }

    /**
     * متوسط أداء كل قسم
     */
    private function categoriesPerformance(Collection $rated): Collection
    {
        return $rated->groupBy('cat_name')
            ->map(fn ($items, $name) => [
                'name'  => $name,
                'score' => round($items->avg('score'), 1),
                'count' => $items->count(),
            ])
            ->sortByDesc('score')
            ->values();
    }

    /**
     * البيانات المرسلة إلى n8n لكتابة التقرير
     */
    private function reportPayload(array $data): array
    {
        $describe = fn ($p) => [
            'name'      => $p->p_name,
            'category'  => $p->cat_name,
            'score'     => (float) $p->score,
            'views'     => $p->views,
            'cart_adds' => $p->cart_adds,
            'sales'     => $p->sales,
            'reason'    => $data['diagnoses'][$p->diagnosis]['label'] ?? '',
        ];

        return [
            'analysis_date'  => $data['analysisDate'],
            'summary'        => $data['cards'],
            'top_products'   => $data['topProducts']->map($describe),
            'worst_products' => $data['worstProducts']->map($describe),
            'categories'     => $data['charts']['categories'],
            'sizes'          => $data['charts']['sizes'],
            'colors'         => $data['charts']['colors'],
            'funnel'         => $data['charts']['funnel'],
            'trend'          => $data['charts']['trend']->take(-7)->values(),
        ];
    }
}