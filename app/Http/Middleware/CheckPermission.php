<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $requiredPermission): Response
    {
        // 1. التأكد أن المستخدم مسجل دخوله
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. جلب صلاحيات المستخدم (نفس كودك السابق)
        $userPrivileges = Auth::user()->userPermissions()
                            ->where('is_active', 1)
                            ->with('permission')
                            ->get()
                            ->pluck('permission.permission_name')
                            ->map(fn($item) => trim($item))
                            ->toArray();

        // 3. الفحص: إذا كان "مدير متجر" (له كل الصلاحيات) أو يملك الصلاحية المطلوبة للرابط
        if (in_array('مدير المتجر', $userPrivileges) || in_array($requiredPermission, $userPrivileges)) {
            return $next($request); // اسمح له بالمرور
        }

        // 4. إذا لم يملك الصلاحية، اطرده
        abort(403, 'عذراً، لا تمتلك صلاحية الوصول لهذا الرابط');
    }
}