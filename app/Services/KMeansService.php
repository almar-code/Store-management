<?php

namespace App\Services;

/**
 * خوارزمية K-Means لتقسيم المنتجات إلى مجموعات متشابهة
 */
class KMeansService
{
    /**
     * تقسيم النقاط إلى k مجموعات
     *
     * @param array $points  [رقم المنتج => [قيمة1, قيمة2, ...]] والقيم من 0 إلى 1
     * @return array ['labels' => [رقم المنتج => رقم المجموعة], 'centroids' => [مراكز المجموعات]]
     */
    public static function cluster(array $points, int $k = 3, int $maxIterations = 100): array
    {
        $centroids = self::initialCentroids($points, $k);
        $labels = [];

        for ($i = 0; $i < $maxIterations; $i++) {
            // ضم كل نقطة لأقرب مركز
            $newLabels = [];
            foreach ($points as $id => $point) {
                $newLabels[$id] = self::nearest($point, $centroids);
            }

            // التوقف عند ثبات المجموعات
            if ($newLabels === $labels) {
                break;
            }
            $labels = $newLabels;

            // نقل كل مركز إلى متوسط نقاط مجموعته
            foreach ($centroids as $c => $centroid) {
                $members = array_intersect_key($points, array_filter($labels, fn ($label) => $label === $c));
                if (!empty($members)) {
                    $centroids[$c] = self::mean($members);
                }
            }
        }

        return ['labels' => $labels, 'centroids' => $centroids];
    }

    /**
     * اختيار المراكز الأولى بشكل ثابت: نرتب النقاط ونأخذ نقطة من منتصف كل جزء
     * (الثبات يجعل نتيجة التحليل نفسها عند تكراره على نفس البيانات)
     */
    private static function initialCentroids(array $points, int $k): array
    {
        $sorted = array_values($points);
        usort($sorted, fn ($a, $b) => array_sum($a) <=> array_sum($b));

        $n = count($sorted);
        $centroids = [];
        for ($c = 0; $c < $k; $c++) {
            $centroids[$c] = $sorted[min($n - 1, (int) floor(($c + 0.5) * $n / $k))];
        }

        return $centroids;
    }

    /**
     * رقم أقرب مركز للنقطة (المسافة الإقليدية)
     */
    private static function nearest(array $point, array $centroids): int
    {
        $best = 0;
        $bestDistance = INF;

        foreach ($centroids as $c => $centroid) {
            $distance = 0;
            foreach ($point as $d => $value) {
                $distance += ($value - $centroid[$d]) ** 2;
            }
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $c;
            }
        }

        return $best;
    }

    /**
     * متوسط مجموعة نقاط في كل بُعد
     */
    private static function mean(array $points): array
    {
        $count = count($points);
        $sum = array_fill(0, count(reset($points)), 0);

        foreach ($points as $point) {
            foreach ($point as $d => $value) {
                $sum[$d] += $value;
            }
        }

        return array_map(fn ($total) => $total / $count, $sum);
    }
}