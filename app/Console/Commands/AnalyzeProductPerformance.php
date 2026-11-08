<?php

namespace App\Console\Commands;

use App\Services\ProductPerformanceService;
use Illuminate\Console\Command;

class AnalyzeProductPerformance extends Command
{
    protected $signature = 'products:analyze';

    protected $description = 'تحليل أداء المنتجات وحفظ نتيجة اليوم';

    public function handle(): int
    {
        $counts = ProductPerformanceService::run();

        $this->info('تم تحليل أداء المنتجات');
        $this->line('عالي: ' . ($counts['high'] ?? 0));
        $this->line('طبيعي: ' . ($counts['normal'] ?? 0));
        $this->line('راكد: ' . ($counts['stagnant'] ?? 0));
        $this->line('قيد التقييم: ' . ($counts['pending'] ?? 0));

        return self::SUCCESS;
    }
}