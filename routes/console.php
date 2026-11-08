<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\BackupSetting;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('ads:deactivate-expired')->everyMinute();

// النسخ الاحتياطي التلقائي حسب إعدادات لوحة التحكم
$backupSettings = rescue(fn () => BackupSetting::current(), null, false);

if ($backupSettings?->enabled) {
    Schedule::command('store:backup')
        ->dailyAt($backupSettings->run_time)
        ->withoutOverlapping();

    Schedule::command('backup:monitor')->dailyAt('09:00');
}