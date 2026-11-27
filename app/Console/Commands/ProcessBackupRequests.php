<?php

namespace App\Console\Commands;

use App\Jobs\RunRestoreJob;
use App\Models\BackupRestore;
use App\Models\BackupSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * ينفذ طلبات لوحة التحكم الثقيلة في الخلفية كل دقيقة:
 * الاسترجاع والنسخ اليدوي، بعيداً عن عمليات الموقع.
 */
class ProcessBackupRequests extends Command
{
    protected $signature = 'store:backup-requests';

    protected $description = 'تنفيذ طلبات النسخ اليدوي والاسترجاع المسجلة من لوحة التحكم';

    public function handle(): int
    {
        $this->recoverStuck();

        // 1 طلب استرجاع بالانتظار
        $restore = BackupRestore::where('status', 'pending')->oldest()->first();

        if ($restore) {
            $this->info("تنفيذ الاسترجاع #{$restore->id}");
            RunRestoreJob::dispatchSync($restore->id);

            return self::SUCCESS;
        }

        // 2 طلب نسخة يدوية
        $settings = BackupSetting::current();

        if (! $settings->manual_requested_at) {
            return self::SUCCESS;
        }

        $lock = Cache::lock('store-backup-running', 1800);

        // عملية أخرى شغالة، نحاول في الدقيقة التالية
        if (! $lock->get()) {
            return self::SUCCESS;
        }

        try {
            $settings->update(['manual_requested_at' => null]);
            $this->info('تنفيذ نسخة يدوية');
            Artisan::call('store:backup', ['--manual' => true]);
        } finally {
            $lock->release();
        }

        return self::SUCCESS;
    }

    // عمليات استرجاع انقطعت فجأة وبقيت معلقة
    private function recoverStuck(): void
    {
        $stuck = BackupRestore::where('status', 'running')
            ->where('started_at', '<', now()->subMinutes(45))
            ->get();

        if ($stuck->isEmpty()) {
            return;
        }

        foreach ($stuck as $restore) {
            $restore->update([
                'status' => 'failed',
                'message' => 'توقفت العملية بشكل مفاجئ'
                    . ($restore->safety_file ? '، يمكنك استرجاع نسخة الأمان: ' . $restore->safety_file : ''),
                'finished_at' => now(),
            ]);
        }

        // تحرير القفل وإخراج الموقع من الصيانة
        Cache::lock('store-backup-running')->forceRelease();

        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
        }
    }
}