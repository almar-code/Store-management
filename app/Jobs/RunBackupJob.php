<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * يشغّل النسخ اليدوي من زر "إنشاء نسخة الآن".
 * يُستدعى بـ dispatchAfterResponse() فيعمل بعد إرسال الصفحة للمدير
 * ولا يحتاج تشغيل queue:work.
 */
class RunBackupJob
{
    use Dispatchable;

    public function handle(): void
    {
        // يمنع تشغيل نسختين بنفس الوقت لو المدير ضغط الزر مرتين
        Cache::lock('store-backup-running', 600)->get(function () {
            Artisan::call('store:backup', ['--manual' => true]);
        });
    }
}
