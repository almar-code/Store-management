<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        // مشاركة الصلاحيات مع كل صفحات الـ Blade تلقائياً
    View::composer('*', function ($view) {
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

            // نرسل المتغيرات للـ Blade
            $view->with([
                'userPrivileges' => $userPrivileges,
                'isAdmin' => $isAdmin,
                'isStoreManager' => $isStoreManager,
                'isSalesManager' => $isSalesManager
            ]);
        }
    });
    }
}
