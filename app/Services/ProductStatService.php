<?php

namespace App\Services;

use App\Models\ProductStat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * خدمة مركزية لتحديث عدادات أداء المنتجات في جدول product_stats.
 * كل الـ Controllers تستدعي هذه الخدمة بدلاً من تعديل الجدول مباشرة.
 */
class ProductStatService
{
    // الأعمدة المسموح زيادتها فقط (حماية من تمرير اسم عمود خاطئ)
    private const ALLOWED_COLUMNS = [
        'views_count',
        'cart_adds_count',
        'favorites_count',
        'shares_count',
        'sales_count',
        'positive_comments_count',
        'negative_comments_count',
    ];

    // مدة منع تكرار المشاهدة لنفس الشخص على نفس المنتج (بالدقائق)
    private const VIEW_COOLDOWN_MINUTES = 10;

    /**
     * إنشاء صف الإحصائيات للمنتج إن لم يكن موجوداً
     */
    public static function ensure(int $productId): void
    {
        ProductStat::firstOrCreate(['p_id' => $productId]);
    }

    /**
     * زيادة عداد معين بمقدار محدد (زيادة ذرية Atomic داخل قاعدة البيانات)
     * أي خطأ هنا يُسجَّل فقط ولا يوقف العملية الأساسية (المفضلة / السلة / الطلب)
     */
    public static function increment(int $productId, string $column, int $amount = 1): void
    {
        if (!in_array($column, self::ALLOWED_COLUMNS, true) || $amount <= 0) {
            return;
        }

        try {
            self::ensure($productId);

            ProductStat::where('p_id', $productId)->increment($column, $amount);
        } catch (\Throwable $e) {
            Log::warning('ProductStatService increment failed', [
                'p_id'   => $productId,
                'column' => $column,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * تسجيل مشاهدة مع منع التكرار باستخدام Laravel Cache
     * المفتاح يعتمد على رقم العميل، وإن لم يوجد نعتمد على عنوان IP
     *
     * @return bool true إذا تم احتساب المشاهدة، false إذا كانت مكررة
     */
    public static function recordView(int $productId, ?int $customerId, ?string $ip): bool
    {
        $viewer   = $customerId ? 'c' . $customerId : 'ip' . $ip;
        $cacheKey = "product_viewed:{$productId}:{$viewer}";

        // add() لا تكتب إلا إذا كان المفتاح غير موجود → تُرجع false عند التكرار
        $isNewView = Cache::add($cacheKey, true, now()->addMinutes(self::VIEW_COOLDOWN_MINUTES));

        if ($isNewView) {
            self::increment($productId, 'views_count');
        }

        return $isNewView;
    }

        /**
     * تصنيف التعليق وزيادة العداد المناسب
     * 1. الأولوية لحقل sentiment القادم من التطبيق (Positive / Negative / Neutral)
     * 2. احتياط: إذا كان sentiment فارغاً أو بقيمة غير معروفة نعتمد على النجوم
     *    4 - 5 نجوم = إيجابي | 1 - 2 = سلبي | 3 = محايد (لا يُحسب)
     */
    public static function recordComment(int $productId, ?string $sentiment, int $stars): void
    {
        // توحيد القيمة: إزالة المسافات وتحويلها لأحرف صغيرة (Positive = positive = POSITIVE)
        $sentiment = $sentiment !== null ? mb_strtolower(trim($sentiment)) : '';

        if (in_array($sentiment, ['positive', 'إيجابي', 'ايجابي'], true)) {
            self::increment($productId, 'positive_comments_count');
        } elseif (in_array($sentiment, ['negative', 'سلبي'], true)) {
            self::increment($productId, 'negative_comments_count');
        } else {
            // احتياط: لا توجد قيمة sentiment (أو قيمة غير معروفة) → نعتمد على النجوم
            if ($stars >= 4) {
                self::increment($productId, 'positive_comments_count');
            } elseif ($stars <= 2) {
                self::increment($productId, 'negative_comments_count');
            }
        }
    }
}