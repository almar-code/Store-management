<?php

namespace App\Console\Commands;

use App\Models\BackupSetting;
use Illuminate\Console\Command;

class StoreBackupCommand extends Command
{
    // --manual : نسخة يدوية من زر "إنشاء نسخة الآن" (يبدأ اسم ملفها بـ manual-)
    protected $signature = 'store:backup {--manual}';

    protected $description = 'نسخ قاعدة البيانات ثم تنظيف النسخ القديمة حسب إعدادات لوحة التحكم';

    public function handle(): int
    {
        $settings = BackupSetting::current();

        // تطبيق مدة الاحتفاظ اللي اختارها العميل
        config([
            'backup.cleanup.default_strategy.keep_daily_backups_for_days'     => $settings->keep_daily_days,
            'backup.cleanup.default_strategy.keep_weekly_backups_for_weeks'   => $settings->keep_weekly_weeks,
            'backup.cleanup.default_strategy.keep_monthly_backups_for_months' => $settings->keep_monthly_months,
            'backup.cleanup.default_strategy.keep_yearly_backups_for_years'   => $settings->keep_yearly_years,
        ]);

        $options = ['--only-db' => true];

        if ($this->option('manual')) {
            $options['--filename'] = 'manual-' . now()->format('Y-m-d-H-i-s') . '.zip';
        }

        $this->call('backup:run', $options);
        $this->call('backup:clean');

        return self::SUCCESS;
    }
}
