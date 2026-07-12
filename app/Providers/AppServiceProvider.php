<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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
}
}
