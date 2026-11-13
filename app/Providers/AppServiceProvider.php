<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Product;
use App\Observers\ProductObserver;


use App\Models\BackupSetting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\BackupWasSuccessful;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

   
    public function boot(): void
{
    // إنشاء صف إحصائيات تلقائياً لكل منتج جديد
Product::observe(ProductObserver::class);
    // إجبار لارافل على تحويل جميع الروابط والنماذج إلى https في بيئة الإنتاج
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }

    // مشاركة الصلاحيات مع جميع ملفات الـ Blade
    View::composer('*', function ($view) {
        // قيم افتراضية لكي لا ينهار الموقع للزوار غير المسجلين
        $isAdmin = false;
        $isStoreManager = false;
        $isSalesManager = false;
        $userPrivileges = [];

        if (Auth::check()) {
            $userPrivileges = Auth::user()->userPermissions()
                ->where('is_active', 1)
                ->with('permission')
                ->get()
                ->pluck('permission.permission_name')
                ->map(fn($item) => trim($item))
                ->toArray();

            $isAdmin = in_array('مدير المتجر', $userPrivileges);
            $isStoreManager = in_array('مدير المخزن', $userPrivileges) || $isAdmin;
            $isSalesManager = in_array('مدير المبيعات', $userPrivileges) || $isAdmin;
        }

        // الآن نرسل المتغيرات في كل الحالات (سواء مسجل دخول أو لا)
        $view->with([
            'userPrivileges' => $userPrivileges,
            'isAdmin' => $isAdmin,
            'isStoreManager' => $isStoreManager,
            'isSalesManager' => $isSalesManager
        ]);
    });
            // تسجيل حالة آخر نسخة احتياطية
        Event::listen(BackupWasSuccessful::class, function () {
            BackupSetting::current()->update([
                'last_backup_at' => now(),
                'last_backup_status' => 'success',
                'last_backup_message' => null,
            ]);
        });

        Event::listen(BackupHasFailed::class, function (BackupHasFailed $event) {
            BackupSetting::current()->update([
                'last_backup_at' => now(),
                'last_backup_status' => 'failed',
                'last_backup_message' => Str::limit($event->exception->getMessage(), 500),
            ]);
        });
                // ويندوز: تمرير متغيرات النظام لـ mysqldump و mysql عند التشغيل من المتصفح
        if (PHP_OS_FAMILY === 'Windows' && ! app()->runningInConsole()) {
            foreach (getenv() as $key => $value) {
                $_SERVER[$key] ??= $value;
            }
        }
}
}
