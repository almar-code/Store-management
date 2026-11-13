<?php

namespace App\Jobs;

use App\Models\BackupRestore;
use App\Services\BackupRestoreService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * يشغّل الاسترجاع بعد إرسال الصفحة للمدير.
 * المراحل: فحص، نسخة أمان، صيانة، استيراد، تحديث الجداول.
 */
class RunRestoreJob
{
    use Dispatchable;

    public function __construct(public int $restoreId)
    {
    }

    public function handle(BackupRestoreService $service): void
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $restore = BackupRestore::find($this->restoreId);

        if (! $restore || $restore->status !== 'pending') {
            return;
        }

        // نفس قفل النسخ حتى لا تشتغل عمليتين مع بعض
        $lock = Cache::lock('store-backup-running', 1800);

        if (! $lock->get()) {
            $restore->update([
                'status' => 'failed',
                'message' => 'توجد عملية نسخ أو استرجاع أخرى جارية، حاول بعد دقائق',
                'finished_at' => now(),
            ]);

            return;
        }

        $wasDown = app()->isDownForMaintenance();
        $safety = null;
        $imported = false;
        $migrations = [];

        try {
            // 1 فحص النسخة
            $restore->update(['status' => 'running', 'step' => 1, 'started_at' => now(), 'message' => 'جاري فحص النسخة']);
            $sql = $service->prepare($restore->backup_file);

            // 2 نسخة أمان
            $restore->update(['step' => 2, 'message' => 'جاري إنشاء نسخة الأمان']);
            $safety = $service->safetyBackup();

            // 3 الاسترجاع داخل وضع الصيانة
            $restore->update(['step' => 3, 'safety_file' => $safety, 'message' => 'جاري استرجاع البيانات']);
            $migrations = $service->protectedMigrations();

            if (! $wasDown) {
                Artisan::call('down', ['--retry' => 30]);
            }

            $imported = true;
            $service->import($sql);
            DB::reconnect();
            $service->restoreMigrations($migrations);

            // 4 تحديث هيكل الجداول
            $restore->update(['step' => 4, 'message' => 'جاري تحديث هيكل الجداول']);
            $warning = $service->migrate();

            // 5 تم
            $restore->update([
                'status' => 'success',
                'step' => 5,
                'message' => $warning ?? 'تم استرجاع النسخة بنجاح',
                'finished_at' => now(),
            ]);

            // تحديد العملية الأصلية كمُتراجع عنها
            if ($restore->undo_of) {
                BackupRestore::whereKey($restore->undo_of)->update(['status' => 'undone']);
            }
        } catch (Throwable $e) {
            $message = $e->getMessage();

            // لو فشل في المنتصف نرجع البيانات من نسخة الأمان
            if ($imported && $safety) {
                try {
                    $service->import($service->prepare($safety));
                    DB::reconnect();
                    $service->restoreMigrations($migrations);
                    $message .= ' — تمت إعادة البيانات كما كانت قبل الاسترجاع';
                } catch (Throwable) {
                    $message .= ' — تعذرت الإعادة التلقائية، استخدم نسخة الأمان: ' . $safety;
                }
            }

            $restore->update([
                'status' => 'failed',
                'message' => Str::limit($message, 900),
                'finished_at' => now(),
            ]);
        } finally {
            if (! $wasDown) {
                Artisan::call('up');
            }

            $service->cleanup();
            $lock->release();
        }

        $service->notify($restore->fresh());
    }
}
